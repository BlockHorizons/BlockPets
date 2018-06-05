<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets;

use BlockHorizons\BlockPets\events\PetInventoryInitializationEvent;
use BlockHorizons\BlockPets\events\PetLevelUpEvent;
use BlockHorizons\BlockPets\events\PetRespawnEvent;
use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\creatures\EnderDragonPet;
use BlockHorizons\BlockPets\pets\inventory\PetInventory;
use BlockHorizons\BlockPets\pets\inventory\PetInventoryManager;
use BlockHorizons\BlockPets\tasks\PetRespawnTask;
use pocketmine\entity\Attribute;
use pocketmine\entity\Creature;
use pocketmine\entity\Entity;
use pocketmine\entity\Rideable;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\item\Food;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\particle\HeartParticle;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\network\mcpe\protocol\SetEntityLinkPacket;
use pocketmine\network\mcpe\protocol\types\EntityLink;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;

abstract class BasePet extends Creature implements Rideable {

	const STATE_STANDING = 0;
	const STATE_SITTING = 1;

	const TIER_COMMON = 1;
	const TIER_UNCOMMON = 2;
	const TIER_SPECIAL = 3;
	const TIER_EPIC = 4;
	const TIER_LEGENDARY = 5;

	const LINK_RIDING = 0;
	const LINK_RIDER = 1;

	/** @var string */
	public $name = "";
	/** @var float */
	public $scale = 1.0;

	/** @var int */
	protected $petLevel = 0;
	/** @var string */
	protected $petName = "";
	/** @var Player|null */
	protected $rider = null;
	/** @var Vector3 */
	protected $rider_seatpos;
	/** @var bool */
	protected $riding = false;
	/** @var Vector3 */
	protected $seatpos;
	/** @var bool */
	protected $visibility = true;

	/** @var int */
	protected $attackDamage = 4;
	/** @var float */
	protected $speed = 1.0;
	/** @var int */
	protected $petLevelPoints = 0;

	/** @var bool */
	protected $canBeRidden = true;
	/** @var bool */
	protected $canBeChested = true;
	/** @var bool */
	protected $canAttack = true;
	/** @var bool */
	protected $canRide = true;

	/** @var Calculator */
	protected $calculator;

	/** @var float */
	protected $xOffset = 0.0;
	/** @var float */
	protected $yOffset = 0.0;
	/** @var float */
	protected $zOffset = 0.0;

	/** @var EntityLink[] */
	private $links = [];
	/** @var Player */
	private $petOwner = "";
	/** @var bool */
	private $dormant = false;
	/** @var bool */
	private $shouldIgnoreEvent = false;
	/** @var int */
	private $positionSeekTick = 60;
	/** @var PetInventoryManager */
	private $inventory_manager;
	/** @var float */
	private $maxSize = 10.0;

	public function __construct(Level $level, CompoundTag $nbt) {
		$this->petOwner = $level->getServer()->getPlayerExact($nbt->getString("petOwner"));
		if($this->petOwner === null) {
			$this->close();
			return;
		}

		parent::__construct($level, $nbt);
	}

	public function register(): void {
		$this->getLoader()->getDatabase()->registerPet($this);
	}

	public function selectProperties(): void {
		$properties = $this->getLoader()->getPetProperties()->getPropertiesFor($this->getEntityType());
		$this->useProperties($properties);
	}

	/**
	 * Returns the BlockPets Loader. For internal usage.
	 *
	 * @return Loader|null
	 */
	public function getLoader(): ?Loader {
		$plugin = $this->server->getPluginManager()->getPlugin("BlockPets");
		if($plugin instanceof Loader) {
			return $plugin;
		}
		return null;
	}

	/**
	 * Internal.
	 *
	 * @return string
	 */
	public function getEntityType(): string {
		return strtr($this->getName(), [
			" " => "",
			"Pet" => ""
		]);
	}

	/**
	 * Returns the name of the pet type.
	 *
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * @param array $properties
	 */
	public function useProperties(array $properties): void {
		$this->speed = (float) $properties["Speed"];
		$this->canBeRidden = (bool) $properties["Can-Be-Ridden"];
		$this->canBeChested = (bool) $properties["Can-Be-Chested"];
		$this->canAttack = (bool) $properties["Can-Attack"];
		$this->canRide = (bool) $properties["Can-Sit-On-Owner"];
		$this->maxSize = (float) $properties["Max-Size"];
	}

	/**
	 * @return bool
	 */
	public function isChested(): bool {
		return $this->getGenericFlag(self::DATA_FLAG_CHESTED);
	}

	/**
	 * @return bool
	 */
	public function isBaby(): bool {
		return $this->getGenericFlag(self::DATA_FLAG_BABY);
	}

	/**
	 * @param bool $value
	 */
	public function setChested(bool $value = true): void {
		if($this->isChested() !== $value) {
			$this->setGenericFlag(self::DATA_FLAG_CHESTED, $value);
			$loader = $this->getLoader();
			if($loader->getBlockPetsConfig()->storeToDatabase()) {
				$loader->getDatabase()->updateChested($this);
			}
		}
	}

	/**
	 * Levels up the pet's experience level by the given amount. Sends a title if $silent is false or not set.
	 *
	 * @param int  $amount
	 * @param bool $silent
	 *
	 * @return bool
	 */
	public function levelUp(int $amount = 1, bool $silent = false): bool {
		$this->server->getPluginManager()->callEvent($ev = new PetLevelUpEvent($this->getLoader(), $this, $this->getPetLevel(), $this->getPetLevel() + $amount));
		if($ev->isCancelled()) {
			return false;
		}
		$this->setPetLevel($ev->getTo());

		if(!$silent && $this->getPetOwner() !== null) {
			$this->getPetOwner()->addTitle((TextFormat::GREEN . "Level Up!"), (TextFormat::AQUA . "Your pet " . $this->getPetName() . TextFormat::RESET . TextFormat::AQUA . " turned level " . $ev->getTo() . "!"));
		}
		return true;
	}

	/**
	 * Returns the current experience level of the pet.
	 *
	 * @return int
	 */
	public function getPetLevel(): int {
		return $this->petLevel;
	}

	/**
	 * Sets the pet's experience level to the given amount.
	 *
	 * @param int $petLevel
	 */
	public function setPetLevel(int $petLevel): void {
		if($this->petLevel !== $petLevel) {
			$this->petLevel = $petLevel;

			$loader = $this->getLoader();
			if($loader->getBlockPetsConfig()->storeToDatabase()) {
				$loader->getDatabase()->updateExperience($this);
			}
		}
	}

	/**
	 * @return bool
	 */
	public function getVisibility(): bool {
		return $this->visibility;
	}

	/**
	 * @internal
	 * @param bool $value
	 */
	public function updateVisibility(bool $value): void {
		$this->visibility = $value;
		$this->setImmobile(!$value);
		if($value) {
			$this->spawnToAll();
		} else {
			$this->despawnFromAll();
		}
	}

	public function setImmobile(bool $value = true): void {
		if(!$this->visibility && $value) {
			return;
		}
		parent::setImmobile($value);
	}


	public function spawnTo(Player $player): void {
		if(!$this->visibility) {
			return;
		}

		parent::spawnTo($player);
	}

	protected function sendSpawnPacket(Player $player): void {
		$pk = new AddEntityPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->type = static::NETWORK_ID;
		$pk->position = $this->asVector3();
		$pk->motion = $this->getMotion();
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->attributes = $this->attributeMap->getAll();
		$pk->metadata = $this->propertyManager->getAll();
		$pk->links = array_values($this->links);
		$player->dataPacket($pk);
	}

	/**
	 * Returns the player that owns this pet if they are online.
	 *
	 * @return Player
	 */
	final public function getPetOwner(): Player {
		return $this->petOwner;
	}

	/**
	 * Returns the actual name of the pet. Not to be confused with getName(), which returns the pet type name.
	 *
	 * @return string
	 */
	public function getPetName(): string {
		return $this->petName;
	}

	/**
	 * @param EntityDamageEvent $source
	 */
	public function attack(EntityDamageEvent $source): void {
		if(!$this->visibility) {
			return;
		}
		if($source instanceof EntityDamageByEntityEvent) {
			$player = $source->getDamager();
			if($player instanceof Player) {
				$hand = $player->getInventory()->getItemInHand();
				if($hand instanceof Food) {
					if($this->getHealth() === $this->getMaxHealth()) {
						parent::attack($source);
						return;
					}
					$nutrition = $hand->getFoodRestore();
					$heal = (int) ($nutrition / 40 * $this->getMaxHealth() + 2);
					if($this->getHealth() + $heal > $this->getMaxHealth()) {
						$heal = $this->getMaxHealth() - $this->getHealth();
					}
					$hand->pop();
					$player->getInventory()->setItemInHand($hand);
					$this->heal(new EntityRegainHealthEvent($this, $heal, EntityRegainHealthEvent::CAUSE_SATURATION));
					$this->getLevel()->addParticle(new HeartParticle($this->add(0, 2), 4));

					if($this->getLoader()->getBlockPetsConfig()->giveExperienceWhenFed()) {
						$this->addPetLevelPoints($nutrition / 40 * $this->getRequiredLevelPoints($this->getPetLevel()));
					}

					$this->calculator->updateNameTag();
					$source->setCancelled();

				} elseif($hand->getId() === Item::CHEST && $this->canBeChested) {
					if(!$this->isChested() && $this->getPetOwnerName() === $player->getName()) {
						$this->server->getPluginManager()->callEvent($ev = new PetInventoryInitializationEvent($this->getLoader(), $this));
						if(!$ev->isCancelled()) {
							$hand->pop();
							$player->getInventory()->setItemInHand($hand);
							$this->setChested();
							$source->setCancelled();
						}
					}

				} elseif($player->getName() === $this->getPetOwnerName()) {
					if($this->isChested() && $hand->getId() === Item::AIR) {
						$source->setCancelled();
						$this->getInventoryManager()->openAs($player);
					} elseif($player->isSneaking() && $this->canRide) {
						foreach($this->getLoader()->getPetsFrom($player) as $pet) {
							$pet->dismountFromOwner();
						}
						$this->sitOnOwner();
					}
				}
			}
		}
		$this->calculator->updateNameTag();
		parent::attack($source);
	}

	/**
	 * @return bool
	 */
	public function isRiding(): bool {
		return $this->riding;
	}

	/**
	 * Adds the given amount of experience points to the pet. Levels up the pet if required.
	 *
	 * @param float $points
	 *
	 * @return bool
	 */
	public function addPetLevelPoints(float $points): bool {
		$totalPoints = $this->getPetLevelPoints() + (int) $points;
		if($totalPoints >= $this->getRequiredLevelPoints($this->getPetLevel())) {
			$this->setPetLevelPoints($totalPoints - $this->getRequiredLevelPoints($this->getPetLevel()));
			$this->levelUp();
			return true;
		}
		$this->setPetLevelPoints($totalPoints);
		$this->calculator->updateNameTag();
		return false;
	}

	/**
	 * Returns the pet's current experience level points.
	 *
	 * @return int
	 */
	public function getPetLevelPoints(): int {
		return $this->petLevelPoints;
	}

	/**
	 * Sets the pet's experience level points to the given amount.
	 *
	 * @param int $points
	 */
	public function setPetLevelPoints(int $points): void {
		$this->petLevelPoints = $points;
		$loader = $this->getLoader();
		if($loader->getBlockPetsConfig()->storeToDatabase()) {
			$loader->getDatabase()->updateExperience($this);
		}
	}

	/**
	 * Returns the required amount of points for the given level to level up automatically.
	 *
	 * @param int $level
	 *
	 * @return int
	 */
	public function getRequiredLevelPoints(int $level): int {
		return (int) (20 + $level / 1.5 * $level);
	}

	/**
	 * Returns the name of the owner of this pet.
	 *
	 * @return string
	 */
	final public function getPetOwnerName(): string {
		return $this->petOwner->getName();
	}

	/**
	 * Returns the inventory of this pet.
	 *
	 * @return PetInventory
	 */
	public function getInventory(): PetInventory {
		return $this->inventory_manager->getInventory();
	}

	/**
	 * Returns the inventory manager of this pet.
	 *
	 * @return PetInventoryManager
	 */
	public function getInventoryManager(): PetInventoryManager {
		return $this->inventory_manager;
	}

	/**
	 * Internal.
	 *
	 * @return string
	 */
	public function getNameTag(): string {
		return $this->getPetName();
	}

	protected function initEntity(): void {
		parent::initEntity();
		$this->selectProperties();

		$this->petLevel = $this->namedtag->getInt("petLevel", 1);
		$this->petLevelPoints = $this->namedtag->getInt("petLevelPoints", 0);
		$this->petName = $this->namedtag->getString("petName");
		$this->scale = $this->namedtag->getFloat("scale", $this->getScale());
		$this->setGenericFlag(self::DATA_FLAG_CHESTED, (bool) $this->namedtag->getByte("chested", 0));
		$this->setGenericFlag(self::DATA_FLAG_BABY, (bool) $this->namedtag->getByte("isBaby", 0));
		$this->setGenericFlag(self::DATA_FLAG_TAMED, true);

		$this->calculator = new Calculator($this);

		$this->setNameTagVisible(true);
		$this->setNameTagAlwaysVisible(true);

		$this->setScale($this->scale);

		$this->inventory_manager = new PetInventoryManager($this);
		$this->spawnToAll();

		$this->getAttributeMap()->addAttribute(Attribute::getAttribute(20));
		$this->setCanSaveWithChunk(false);

		$this->generateCustomPetData();
		$this->setImmobile();

		$scale = $this->getScale();
		if($this instanceof EnderDragonPet) {
			$this->rider_seatpos = new Vector3(0, 2.65 + $scale, -1.7);
		} elseif($this instanceof SmallCreature) {
			$this->rider_seatpos = new Vector3(0, 0.78 + $scale * 0.9, -0.25);
		} else {
			$this->rider_seatpos = new Vector3(0, 1.8 + $scale * 0.9, -0.25);
		}

		$this->seatpos = new Vector3(0, $scale * 0.4 - 0.3, 0);
	}

	public function generateCustomPetData(): void {

	}

	/**
	 * Returns the network (entity) ID of the entity.
	 *
	 * @return int
	 */
	final public function getNetworkId(): int {
		return static::NETWORK_ID;
	}

	/**
	 * Returns the speed of this pet.
	 *
	 * @return float
	 */
	public function getSpeed(): float {
		return $this->speed;
	}

	/**
	 * @return float
	 */
	public function getStartingScale(): float {
		return $this->scale;
	}

	/**
	 * Returns the attack damage of this pet.
	 *
	 * @return int
	 */
	public function getAttackDamage(): int {
		return $this->attackDamage;
	}

	/**
	 * Sets the attack damage to the given amount.
	 *
	 * @param int $amount
	 */
	public function setAttackDamage(int $amount): void {
		$this->attackDamage = $amount;
	}

	/**
	 * Performs a special action of a pet every tick.
	 */
	public function doPetUpdates(int $currentTick): bool {
		return true;
	}

	protected function applyGravity(): void {
		if($this->isRiding()) {
			return;
		}

		parent::applyGravity();
	}

	protected function broadcastMovement(bool $teleport = false): void {
		if($this->isRiding()) {
			return;
		}

		parent::broadcastMovement($teleport);
	}

	/**
	 * @param $currentTick
	 *
	 * @return bool
	 */
	final public function onUpdate(int $currentTick): bool {
		if(!parent::onUpdate($currentTick) && $this->isClosed()) {
			return false;
		}
		if($this->isRiding()) {
			$petOwner = $this->getPetOwner();

			$x = $petOwner->x - $this->x;
			$y = $petOwner->y - $this->y;
			$z = $petOwner->z - $this->z;

			if($x !== 0.0 || $z !== 0.0 || $y !== -$petOwner->height) {
				$this->fastMove($x, $y + $petOwner->height, $z);
			}
			return false;
		}
		if(!$this->checkUpdateRequirements()) {
			return true;
		}
		if(!$this->isRidden()) {
			if(random_int(1, 60) === 1) {
				if($this->getHealth() !== $this->getMaxHealth()) {
					$this->heal(new EntityRegainHealthEvent($this, 1, EntityRegainHealthEvent::CAUSE_REGEN));
					$this->calculator->updateNameTag();
				}
			}
			$petOwner = $this->getPetOwner();
			if(!$this->isDormant() && ($this->getLevel()->getEntity($petOwner->getId()) === null || $this->distance($petOwner) >= 50)) {
				$this->teleport($petOwner);
				return true;
			}
			++$this->positionSeekTick;
			if($this->shouldFindNewPosition()) {
				if(!$this->getLoader()->getBlockPetsConfig()->shouldStalkPetOwner()) {
					if((bool) random_int(0, 1)) {
						$multiplicationValue = 1;
					} else {
						$multiplicationValue = -1;
					}
					$offset_factor = $multiplicationValue * (3 + $this->getScale());
					$this->xOffset = lcg_value() * $offset_factor;
					$this->yOffset = lcg_value() * $offset_factor;
					$this->zOffset = lcg_value() * $offset_factor;
				}
			}
		}
		$this->doPetUpdates($currentTick);
		return true;
	}

	/**
	 * @return bool
	 */
	public function shouldFindNewPosition(): bool {
		if($this->positionSeekTick >= 60) {
			$this->positionSeekTick = 0;
			return true;
		}
		return false;
	}

	/**
	 * @param bool $ignore
	 */
	public function kill($ignore = false): void {
		$this->shouldIgnoreEvent = $ignore;
		parent::kill();
	}

	/**
	 * Detaches the rider from the pet.
	 *
	 * @return bool
	 */
	public function throwRiderOff(): bool {
		if(!$this->isRidden()) {
			return false;
		}

		$rider = $this->getRider();
		$this->rider = null;
		$rider->canCollide = true;
		$this->removeLink($rider, self::LINK_RIDER);

		$rider->setGenericFlag(self::DATA_FLAG_RIDING, false);
		if($rider->isSurvival()) {
			$rider->setAllowFlight(false);
		}
		$rider->onGround = true;

		$this->width = $this->getDataPropertyManager()->getFloat(self::DATA_BOUNDING_BOX_WIDTH);
		$this->height = $this->getDataPropertyManager()->getFloat(self::DATA_BOUNDING_BOX_HEIGHT);
		$this->recalculateBoundingBox();
		return true;
	}

	/**
	 * Returns the rider of the pet if it has a rider, and null if this is not the case.
	 *
	 * @return Player|null
	 */
	public function getRider(): ?Player {
		return $this->rider;
	}

	/**
	 * Sets the given player as rider on the pet, connecting it to it and initializing some things.
	 *
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function setRider(Player $player): bool {
		if($this->isRidden()) {
			return false;
		}

		$this->rider = $player;
		$player->canCollide = false;
		$owner = $this->getPetOwner();
		$player->getDataPropertyManager()->setVector3(self::DATA_RIDER_SEAT_POSITION, $this->rider_seatpos);

		$this->addLink($player, self::LINK_RIDER);

		$player->setGenericFlag(self::DATA_FLAG_RIDING, true);
		$this->setGenericFlag(self::DATA_FLAG_SADDLED, true);

		if($owner->isSurvival()) {
			$owner->setAllowFlight(true); // Set allow flight to true to prevent any 'kicked for flying' issues.
		}

		$this->width = max($player->width, $this->width);//adding more vertical area to the BB, so the horizontal can just be the maximum.
		$this->height = max(($this->rider_seatpos->y / 2.5) + $player->height, $this->height);
		$this->recalculateBoundingBox();
		return true;
	}

	/**
	 * Heals the current pet back to full health.
	 */
	public function fullHeal(): bool {
		$health = $this->getHealth();
		$maxHealth = $this->getMaxHealth();
		if($health === $maxHealth) {
			return false;
		}
		$diff = $maxHealth - $health;
		$this->heal(new EntityRegainHealthEvent($this, $diff, EntityRegainHealthEvent::CAUSE_CUSTOM));
		return true;
	}

	/**
	 * @param string $newName
	 */
	public function changeName(string $newName): void {
		$database = $this->getLoader()->getDatabase();
		$database->unregisterPet($this);
		$this->petName = $newName;
		$this->getCalculator()->updateNameTag();
		$database->registerPet($this);
		$this->getInventoryManager()->setName($newName);
	}

	/**
	 * Returns the calculator connected to this pet, used to recalculate health, size, experience etc.
	 *
	 * @return Calculator
	 */
	public function getCalculator(): Calculator {
		return $this->calculator;
	}

	/**
	 * @return bool
	 */
	public function shouldIgnoreEvent(): bool {
		return $this->shouldIgnoreEvent;
	}

	/**
	 * @param float $motionX
	 * @param float $motionZ
	 *
	 * @return bool
	 */
	public abstract function doRidingMovement(float $motionX, float $motionZ): void;

	/**
	 * @return bool
	 */
	protected function checkUpdateRequirements(): bool {
		if(!$this->visibility) {
			return false;
		}
		if($this->isDormant()) {
			$this->despawnFromAll();
			return false;
		}
		if($this->getPetOwner()->isClosed()) {
			$this->rider = null;
			$this->riding = false;
			$this->despawnFromAll();
			$this->setDormant();
			$this->close();
			return false;
		}
		if(!$this->getPetOwner()->isAlive()) {
			return false;
		}
		return true;
	}

	public function close(): void {
		if(!$this->closed) {
			$loader = $this->getLoader();
			if(!$loader->getBlockPetsConfig()->storeToDatabase()) {
				$loader->getDatabase()->unregisterPet($this);
				$loader->removePet($this, false);
			}
			parent::close();
		}
	}

	public function onDeath(): void {
		parent::onDeath();
		$loader = $this->getLoader();
		$delay = $loader->getBlockPetsConfig()->getRespawnTime();

		$loader->getDatabase()->unregisterPet($this);
		if($this->shouldIgnoreEvent()) {
			return;
		}

		$this->server->getPluginManager()->callEvent($ev = new PetRespawnEvent($loader, $this, $delay));
		if($ev->isCancelled()) {
			return;
		}

		$newPet = $loader->clonePet($this);
		$newPet->register();

		$delay = $ev->getDelay() * 20;
		$this->server->getScheduler()->scheduleDelayedTask(new PetRespawnTask($loader, $newPet), $delay);
		$newPet->despawnFromAll();
		$newPet->setDormant();
	}

	/**
	 * Returns whether this pet is being ridden or not.
	 *
	 * @return bool
	 */
	public function isRidden(): bool {
		return $this->rider !== null;
	}

	/**
	 * Returns whether this pet is dormant or not. If this pet is dormant, it will not move.
	 *
	 * @return bool
	 */
	public function isDormant(): bool {
		return $this->dormant;
	}

	/**
	 * Sets the dormant state to this pet with the given value.
	 *
	 * @param bool $value
	 */
	public function setDormant(bool $value = true): void {
		$this->dormant = $value;
	}

	/**
	 * Adds a link to this pet.
	 *
	 * @param Entity $entity
	 * @param int $type
	 */
	public function addLink(Entity $entity, int $type): void {
		$this->removeLink($entity, $type);
		$viewers = $this->getViewers();

		switch($type) {
			case self::LINK_RIDER:
				$link = new EntityLink();
				$link->fromEntityUniqueId = $this->getId();
				$link->type = self::STATE_SITTING;
				$link->toEntityUniqueId = $entity->getId();
				$link->bool1 = true;

				if($entity instanceof Player) {
					$pk = new SetEntityLinkPacket();
					$pk->link = $link;
					$entity->dataPacket($pk);

					$link_2 = new EntityLink();
					$link_2->fromEntityUniqueId = $this->getId();
					$link_2->type = self::STATE_SITTING;
					$link_2->toEntityUniqueId = 0;
					$link_2->bool1 = true;

					$pk = new SetEntityLinkPacket();
					$pk->link = $link_2;
					$entity->dataPacket($pk);
					unset($viewers[$entity->getLoaderId()]);
				}
				break;
			case self::LINK_RIDING:
				$link = new EntityLink();
				$link->fromEntityUniqueId = $entity->getId();
				$link->type = self::STATE_SITTING;
				$link->toEntityUniqueId = $this->getId();
				$link->bool1 = true;

				if($entity instanceof Player) {
					$pk = new SetEntityLinkPacket();
					$pk->link = $link;
					$entity->dataPacket($pk);

					$link_2 = new EntityLink();
					$link_2->fromEntityUniqueId = $entity->getId();
					$link_2->type = self::STATE_SITTING;
					$link_2->toEntityUniqueId = 0;
					$link_2->bool1 = true;

					$pk = new SetEntityLinkPacket();
					$pk->link = $link_2;
					$entity->dataPacket($pk);
					unset($viewers[$entity->getLoaderId()]);
				}
				break;
			default:
				throw new \InvalidArgumentException();
		}

		if(!empty($viewers)) {
			$pk = new SetEntityLinkPacket();
			$pk->link = $link;
			$this->server->broadcastPacket($viewers, $pk);
		}

		$this->links[$type] = $link;
	}

	/**
	 * Removes a link from this pet.
	 *
	 * @param Entity $entity
	 * @param int $type
	 */
	public function removeLink(Entity $entity, int $type): void {
		if(!isset($this->links[$type])) {
			return;
		}

		$viewers = $this->getViewers();

		switch($type) {
			case self::LINK_RIDER:
				$link = new EntityLink();
				$link->fromEntityUniqueId = $this->getId();
				$link->type = self::STATE_STANDING;
				$link->toEntityUniqueId = $entity->getId();
				$link->bool1 = true;

				if($entity instanceof Player) {
					$pk = new SetEntityLinkPacket();
					$pk->link = $link;
					$entity->dataPacket($pk);

					$link_2 = new EntityLink();
					$link_2->fromEntityUniqueId = $entity->getId();
					$link_2->type = self::STATE_STANDING;
					$link_2->toEntityUniqueId = 0;
					$link_2->bool1 = true;

					$pk = new SetEntityLinkPacket();
					$pk->link = $link_2;
					$entity->dataPacket($pk);
					unset($viewers[$entity->getLoaderId()]);
				}
				break;
			case self::LINK_RIDING:
				$link = new EntityLink();
				$link->fromEntityUniqueId = $entity->getId();
				$link->type = self::STATE_STANDING;
				$link->toEntityUniqueId = $this->getId();
				$link->bool1 = true;

				if($entity instanceof Player) {
					$pk = new SetEntityLinkPacket();
					$pk->link = $link;
					$entity->dataPacket($pk);

					$link_2 = new EntityLink();
					$link_2->fromEntityUniqueId = $entity->getId();
					$link_2->type = self::STATE_STANDING;
					$link_2->toEntityUniqueId = 0;
					$link_2->bool1 = true;

					$pk = new SetEntityLinkPacket();
					$pk->link = $link_2;
					$entity->dataPacket($pk);
					unset($viewers[$entity->getLoaderId()]);
				}
				break;
			default:
				throw new \InvalidArgumentException();
		}

		unset($this->links[$type]);

		if(!empty($viewers)) {
			$pk = new SetEntityLinkPacket();
			$pk->link = $link;
			$this->server->broadcastPacket($viewers, $pk);
		}
	}

	/**
	 * @return bool
	 */
	public function sitOnOwner(): bool {
		if($this->riding) {
			return false;
		}
		$this->riding = true;
		$this->getDataPropertyManager()->setVector3(self::DATA_RIDER_SEAT_POSITION, $this->seatpos);
		$this->setGenericFlag(self::DATA_FLAG_RIDING, true);
		$this->setGenericFlag(self::DATA_FLAG_SADDLED, false);

		$this->addLink($this->getPetOwner(), self::LINK_RIDING);
		return true;
	}

	/**
	 * @return bool
	 */
	public function dismountFromOwner(): bool {
		if(!$this->riding) {
			return false;
		}
		$this->riding = false;
		$this->setGenericFlag(self::DATA_FLAG_RIDING, false);
		$petOwner = $this->getPetOwner();
		$this->removeLink($petOwner, self::LINK_RIDING);
		$this->teleport($petOwner);
		return true;
	}

	/**
	 * @return float
	 */
	public function getMaxSize(): float {
		return $this->maxSize;
	}
}
