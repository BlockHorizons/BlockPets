<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets;

use BlockHorizons\BlockPets\events\PetInventoryInitializationEvent;
use BlockHorizons\BlockPets\events\PetLevelUpEvent;
use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\creatures\EnderDragonPet;
use BlockHorizons\BlockPets\pets\inventory\PetInventoryHolder;
use pocketmine\entity\Attribute;
use pocketmine\entity\Creature;
use pocketmine\entity\Rideable;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\item\Food;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\particle\HeartParticle;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\SetEntityLinkPacket;
use pocketmine\network\mcpe\protocol\types\EntityLink;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\math\Vector3;

abstract class BasePet extends Creature implements Rideable {

	const STATE_SITTING = 1;
	const STATE_STANDING = 0;

	const TIER_COMMON = 1;
	const TIER_UNCOMMON = 2;
	const TIER_SPECIAL = 3;
	const TIER_EPIC = 4;
	const TIER_LEGENDARY = 5;

	/** @var string */
	public $name = "";
	/** @var float */
	public $scale = 0.0;

	/** @var string */
	protected $petOwner = "";
	/** @var int */
	protected $petLevel = 0;
	/** @var string */
	protected $petName = "";
	/** @var bool */
	protected $ridden = false;
	/** @var null|string */
	protected $rider = null;
	/** @var bool */
	protected $riding = false;

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
	protected $calculator = null;

	/** @var float */
	protected $xOffset = 0.0;
	/** @var float */
	protected $yOffset = 0.0;
	/** @var float */
	protected $zOffset = 0.0;

	/** @var bool */
	private $dormant = false;
	/** @var bool */
	private $chested = false;
	/** @var bool */
	private $shouldIgnoreEvent = false;
	/** @var int */
	private $positionSeekTick = 60;
	/** @var PetInventoryHolder */
	private $inventory = null;
	/** @var float */
	private $maxSize = 10.0;

	public function __construct(Level $level, CompoundTag $nbt) {
		parent::__construct($level, $nbt);
		$this->selectProperties();

		$this->calculator = new Calculator($this);

		$this->setNameTagVisible(true);
		$this->setNameTagAlwaysVisible(true);

		$this->petLevel = $this->namedtag->getInt("petLevel") - 1;
		$this->petOwner = $this->namedtag->getString("petOwner");
		$this->scale = $this->namedtag->getFloat("scale");
		$this->petName = $this->namedtag->getString("petName");
		$this->petLevelPoints = $this->namedtag->getInt("petLevelPoints");
		$this->chested = (bool) $this->namedtag->getByte("chested");

		if($this->namedtag->getByte("isBaby", 0)) {
			$this->setScale(0.5);
		}else{
			$this->setScale($this->scale);
		}

		$this->setGenericFlag(self::DATA_FLAG_CHESTED, (bool) $this->isChested());
		$this->setGenericFlag(self::DATA_FLAG_BABY, (bool) $this->namedtag->getByte("isBaby", 0));
		$this->setGenericFlag(self::DATA_FLAG_TAMED, true);

		$this->inventory = new PetInventoryHolder($this);
		$this->levelUp(1, true);
		$this->spawnToAll();

		$this->getAttributeMap()->addAttribute(Attribute::getAttribute(20));
	}

	public function selectProperties(): void {
		$properties = $this->getLoader()->getPetProperties()->getPropertiesFor($this->getEntityType());
		$this->useProperties($properties);
	}

	/**
	 * Returns the BlockPets Loader. For internal usage.
	 *
	 * @return Loader
	 */
	public function getLoader(): Loader {
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
		return str_replace(" ", "", str_replace("Pet", "", $this->getName()));
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
		return $this->chested;
	}

	/**
	 * @param bool $value
	 */
	public function setChested(bool $value = true): void {
		$this->setGenericFlag(self::DATA_FLAG_CHESTED, $value);
		$this->chested = $value;
		$this->getLoader()->getDatabase()->updateChested($this->getPetName(), $this->getPetOwnerName());
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

		$this->calculator->recalculateAll();

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
		$this->petLevel = $petLevel;
	}

	/**
	 * Returns the player that owns this pet if they are online, and null if not.
	 *
	 * @return Player|null
	 */
	public function getPetOwner(): ?Player {
		return $this->server->getPlayerExact($this->petOwner);
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
	 * @param float             $damage
	 * @param EntityDamageEvent $source
	 */
	public function attack(EntityDamageEvent $source): void {
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
					$remainder = $hand;
					$remainder->setCount($remainder->getCount() - 1);
					$player->getInventory()->setItemInHand($remainder);
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
							$remainder = $hand;
							$remainder->setCount($remainder->getCount() - 1);
							$player->getInventory()->setItemInHand($remainder);
							$this->setChested();
							$source->setCancelled();
						}
					}

				} elseif($player->getName() === $this->getPetOwnerName()) {
					if($this->isChested() && $hand->getId() === Item::AIR) {
						$source->setCancelled();
						$this->getInventory()->openToOwner();
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
	public function getPetOwnerName(): string {
		return $this->petOwner;
	}

	/**
	 * Returns the inventory holder of this pet.
	 *
	 * @return PetInventoryHolder
	 */
	public function getInventory(): PetInventoryHolder {
		return $this->inventory;
	}

	/**
	 * Internal.
	 *
	 * @return string
	 */
	public function getNameTag(): string {
		return $this->getPetName();
	}

	public function initEntity(): void {
		parent::initEntity();
		$this->generateCustomPetData();
		$this->setImmobile();
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

	public function saveNBT(): void {
		parent::saveNBT();
		$this->namedtag->setString("petOwner", $this->getPetOwnerName());
		$this->namedtag->setString("petName", $this->getPetName());
		$this->namedtag->setFloat("speed", $this->getSpeed());
		$this->namedtag->setFloat("scale", $this->getStartingScale());
		$this->namedtag->setInt("petLevel", $this->getPetLevel());
		$this->namedtag->setInt("petLevelPoints", $this->getPetLevelPoints());
		$this->namedtag->setByte("chested", (int) $this->isChested());
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
	 *
	 * @return bool
	 */
	public function doTickAction(): bool {
		return false;
	}

	/**
	 * @param $currentTick
	 *
	 * @return bool
	 */
	public function onUpdate(int $currentTick): bool {
		$petOwner = $this->getPetOwner();
		if(random_int(1, 60) === 1 && $this->isAlive()) {
			if($this->getHealth() !== $this->getMaxHealth()) {
				$this->heal(new EntityRegainHealthEvent($this, 1, EntityRegainHealthEvent::CAUSE_REGEN));
				$this->calculator->updateNameTag();
			}
		}
		if(!$this->isAlive()) {
			return parent::onUpdate($currentTick);
		}
		if($this->getLevel()->getId() !== $petOwner->getLevel()->getId()) {
			$newPet = $this->getLoader()->createPet($this->getEntityType(), $petOwner, $this->getPetName(), $this->getStartingScale(), (bool) $this->namedtag->getByte("isBaby"), $this->getPetLevel(), $this->getPetLevelPoints(), $this->isChested());
			$newPet->getInventory()->setInventoryContents($this->getInventory()->getInventoryContents());
			$this->close();
			return false;
		}
		if($this->distance($petOwner) >= 50 && !$this->isDormant()) {
			$this->teleport($petOwner);
			return true;
		}
		$this->positionSeekTick++;
		if($this->shouldFindNewPosition()) {
			if(!$this->getLoader()->getBlockPetsConfig()->shouldStalkPetOwner()) {
				if((bool) random_int(0, 1)) {
					$multiplicationValue = 1;
				} else {
					$multiplicationValue = -1;
				}
				$this->xOffset = lcg_value() * $multiplicationValue * (3 + $this->getScale());
				$this->yOffset = lcg_value() * $multiplicationValue * (3 + $this->getScale());
				$this->zOffset = lcg_value() * $multiplicationValue * (3 + $this->getScale());
			}
		}
		$this->doTickAction();
		$this->updateMovement();
		parent::onUpdate($currentTick);
		return $this->isAlive();
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
		if(!$this->ridden) {
			return false;
		}
		$pk = new SetEntityLinkPacket();
		$link = new EntityLink();
		$link->fromEntityUniqueId = $this->getId();
		$link->type = self::STATE_STANDING;
		$link->toEntityUniqueId = $this->getPetOwner()->getId();
		$link->bool1 = true;

		$pk->link = $link;
		$this->ridden = false;
		$rider = $this->getRider();
		$this->rider = null;
		$this->getPetOwner()->canCollide = true;
		$this->server->broadcastPacket($this->getViewers(), $pk);

		$pk = new SetEntityLinkPacket();

		$link = new EntityLink();
		$link->fromEntityUniqueId = $this->getPetOwner()->getId();
		$link->type = self::STATE_STANDING;
		$link->toEntityUniqueId = 0;
		$link->bool1 = true;

		$pk->link = $link;
		$this->getPetOwner()->dataPacket($pk);
		if($this->getPetOwner() !== null) {
			$rider->setGenericFlag(self::DATA_FLAG_RIDING, false);
			if($this->getPetOwner()->isSurvival()) {
				$rider->setAllowFlight(false);
			}
		}
		$rider->onGround = true;
		return true;
	}

	/**
	 * Returns the rider of the pet if it has a rider, and null if this is not the case.
	 *
	 * @return Player|null
	 */
	public function getRider(): ?Player {
		return $this->server->getPlayerExact($this->rider);
	}

	/**
	 * Sets the given player as rider on the pet, connecting it to it and initializing some things.
	 *
	 * @param Player $player
	 *
	 * @return bool
	 */
	public function setRider(Player $player): bool {
		if($this->ridden) {
			return false;
		}
		$this->ridden = true;
		$this->rider = $player->getName();
		$player->canCollide = false;
		$this->getPetOwner()->getDataPropertyManager()->setVector3(self::DATA_RIDER_SEAT_POSITION, new Vector3(0, 1.8 + $this->getScale() * 0.9, -0.25));
		if($this instanceof EnderDragonPet) {
			$player->getDataPropertyManager()->setVector3(self::DATA_RIDER_SEAT_POSITION, new Vector3(0, 2.65 + $this->getScale(), -1.7));
		} elseif($this instanceof SmallCreature) {
			$player->getDataPropertyManager()->setVector3(self::DATA_RIDER_SEAT_POSITION, new Vector3(0, 0.78 + $this->getScale() * 0.9, -0.25));
		}
		$player->setGenericFlag(self::DATA_FLAG_RIDING, true);
		$this->setGenericFlag(self::DATA_FLAG_SADDLED, true);

		$pk = new SetEntityLinkPacket();
		$link = new EntityLink();
		$link->fromEntityUniqueId = $this->getId();
		$link->type = self::STATE_SITTING;
		$link->toEntityUniqueId = $player->getId();
		$link->bool1 = true;

		$pk->link = $link;
		$this->server->broadcastPacket($this->getViewers(), $pk);

		$pk = new SetEntityLinkPacket();
		$link = new EntityLink();
		$link->fromEntityUniqueId = $this->getId();
		$link->type = self::STATE_SITTING;
		$link->toEntityUniqueId = 0;
		$link->bool1 = true;

		$pk->link = $link;
		$player->dataPacket($pk);

		if($this->getPetOwner()->isSurvival()) {
			$this->getPetOwner()->setAllowFlight(true); // Set allow flight to true to prevent any 'kicked for flying' issues.
		}
		return true;
	}

	/**
	 * Heals the current pet back to full health.
	 */
	public function fullHeal(): bool {
		if($this->getHealth() === $this->getMaxHealth()) {
			return false;
		}
		$diff = $this->getMaxHealth() - $this->getHealth();
		$this->heal(new EntityRegainHealthEvent($this, $diff, EntityRegainHealthEvent::CAUSE_CUSTOM));
		return true;
	}

	/**
	 * @param string $newName
	 */
	public function changeName(string $newName): void {
		$this->getLoader()->getDatabase()->unregisterPet($this->getPetName(), $this->getPetOwnerName());
		$this->petName = $newName;
		$this->getLoader()->getDatabase()->registerPet($this);
		$this->getCalculator()->updateNameTag();
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
	public abstract function doRidingMovement(float $motionX, float $motionZ): bool;

	/**
	 * @return bool
	 */
	protected function checkUpdateRequirements(): bool {
		if($this->closed) {
			return false;
		}
		if($this->isRidden()) {
			$this->doTickAction();
			return false;
		}
		if(!$this->isAlive()) {
			// All entities except players get closed automatically. No need to close it manually.
			$this->getLoader()->getDatabase()->unregisterPet($this->getPetName(), $this->getPetOwnerName());

			return true;
		}
		if($this->isDormant()) {
			$this->despawnFromAll();
			return false;
		}
		if($this->getPetOwner() === null) {
			$this->ridden = false;
			$this->rider = null;
			$this->riding = false;
			$this->despawnFromAll();
			$this->setDormant();
			if($this->getLoader()->getBlockPetsConfig()->fetchFromDatabase()) {
				$this->getCalculator()->storeToDatabase();
				$this->close();
			}
			return false;
		}
		if(!$this->getPetOwner()->isAlive()) {
			return false;
		}
		return true;
	}

	/**
	 * Returns whether this pet is being ridden or not.
	 *
	 * @return bool
	 */
	public function isRidden(): bool {
		return $this->ridden;
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
	 * @return bool
	 */
	public function sitOnOwner(): bool {
		if($this->riding) {
			return false;
		}
		$this->riding = true;
		$this->getDataPropertyManager()->setVector3(self::DATA_RIDER_SEAT_POSITION, new Vector3(0, $this->getScale() * 0.4 - 0.3, 0));
		$this->setGenericFlag(self::DATA_FLAG_RIDING, true);

		$pk = new SetEntityLinkPacket();
		$link = new EntityLink();
		$link->fromEntityUniqueId = $this->getPetOwner()->getId();
		$link->type = self::STATE_SITTING;
		$link->toEntityUniqueId = $this->getId();
		$link->bool1 = true;

		$pk->link = $link;
		$this->server->broadcastPacket($this->getViewers(), $pk);
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

		$pk = new SetEntityLinkPacket();
		$link = new EntityLink();
		$link->fromEntityUniqueId = $this->getPetOwner()->getId();
		$link->type = self::STATE_STANDING;
		$link->toEntityUniqueId = $this->getId();
		$link->bool1 = true;

		$pk->link = $link;
		$this->server->broadcastPacket($this->getViewers(), $pk);
		$this->teleport($this->getPetOwner());
		return true;
	}

	/**
	 * @return float
	 */
	public function getMaxSize(): float {
		return $this->maxSize;
	}
}
