<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets;

use BlockHorizons\BlockPets\events\PetInventoryInitializationEvent;
use BlockHorizons\BlockPets\events\PetLevelUpEvent;
use BlockHorizons\BlockPets\events\PetRespawnEvent;
use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\creatures\EnderDragonPet;
use BlockHorizons\BlockPets\pets\inventory\PetInventoryManager;
use BlockHorizons\BlockPets\tasks\PetRespawnTask;
use pocketmine\entity\Attribute;
use pocketmine\entity\AttributeFactory;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\inventory\Inventory;
use pocketmine\item\Food;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\SetActorLinkPacket;
use pocketmine\network\mcpe\protocol\types\entity\Attribute as NetworkAttribute;
use pocketmine\network\mcpe\protocol\types\entity\EntityLink;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataCollection;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\particle\HeartParticle;
use function array_map;
use function array_values;
use function get_class;
use function lcg_value;
use function max;
use function random_int;

abstract class BasePet extends Living {

	const STATE_STANDING = 0;
	const STATE_SITTING  = 1;

	const TIER_COMMON    = 1;
	const TIER_UNCOMMON  = 2;
	const TIER_SPECIAL   = 3;
	const TIER_EPIC      = 4;
	const TIER_LEGENDARY = 5;

	const LINK_RIDING = 0;
	const LINK_RIDER  = 1;

	const NETWORK_ID      = -1;
	const NETWORK_NAME    = null;
	const NETWORK_ORIG_ID = null;
	
	public const ALLAY = "minecraft:allay";
	public const AXOLOTL = "minecraft:axolotl";
	public const BEE = "minecraft:bee";
	public const FOX = "minecraft:fox";
	public const GOAT = "minecraft:goat";
	public const WARDEN = "minecraft:warden";

	protected float $height = 0.0;
	protected float $width = 0.0;

	/** @var float */
	protected $scale = 1.0;

	protected string $name = "";
	protected int $petLevel = 0;
	protected string $petName = "";
	protected ?Player $rider = null;
	protected Vector3 $riderSeatPos;
	protected bool $riding = false;
	protected Vector3 $seatPos;
	protected bool $visibility = true;
	protected bool $chested = false;
	protected bool $baby = false;
	protected bool $saddled = false;

	protected int $attackDamage = 4;
	protected float $speed = 1.0;
	protected int $petLevelPoints = 0;

	protected bool $canBeRidden = true;
	protected bool $canBeChested = true;
	protected bool $canAttack = true;
	protected bool $canRide = true;

	protected Calculator $calculator;

	protected float $xOffset = 0.0;
	protected float $yOffset = 0.0;
	protected float $zOffset = 0.0;

	/** @var EntityLink[] */
	private $links = [];

	private ?Player $petOwner = null;
	private bool $dormant = false;
	private int $positionSeekTick = 60;
	private PetInventoryManager $inventory_manager;
	private float $maxSize = 10.0;
	private bool $dead = false;

	final public function __construct(Location $location, ?CompoundTag $nbt = null) {
		if(static::NETWORK_ID !== -1) {
			throw new \LogicException("Network IDs of pets cannot be overridden.");
		}
		if(static::NETWORK_NAME === null) {
			throw new \LogicException("NETWORK_NAME constant in " . get_class($this) . " must be defined.");
		}
		if(static::NETWORK_ORIG_ID === null) {
			throw new \LogicException("NETWORK_ORIG_ID constant in " . get_class($this) . " must be defined.");
		}
		$this->petOwner = Server::getInstance()->getPlayerExact($nbt->getString("petOwner"));
		if($this->petOwner === null) {
			$this->close();
			return;
		}

		parent::__construct($location, $nbt);
	}

	protected function initEntity(CompoundTag $nbt): void {
		parent::initEntity($nbt);

		$this->selectProperties();

		$this->petLevel = $nbt->getInt("petLevel", 1);
		$this->petLevelPoints = $nbt->getInt("petLevelPoints", 0);
		$this->petName = $nbt->getString("petName");
		$this->scale = $nbt->getFloat("scale", 1.0);
		$this->chested = (bool) $nbt->getByte("chested", 0);
		$this->baby = (bool) $nbt->getByte("isBaby", 0);

		$this->calculator = new Calculator($this);

		$this->setNameTagVisible(true);
		$this->setNameTagAlwaysVisible(true);

		$this->setScale($this->scale);

		$this->inventory_manager = new PetInventoryManager($this);
		$this->spawnToAll();

		$this->getAttributeMap()->add(AttributeFactory::getInstance()->get(Attribute::HORSE_JUMP_STRENGTH));
		$this->setCanSaveWithChunk(false);

		$this->generateCustomPetData();
		$this->setImmobile();

		$scale = $this->getScale();
		if($this instanceof EnderDragonPet) {
			$this->riderSeatPos = new Vector3(-0.5, 3.35 + $scale, -1.7);
		} elseif($this instanceof SmallCreature) {
			$this->riderSeatPos = new Vector3(0, 0.78 + $scale * 0.9, -0.25);
		} else {
			$this->riderSeatPos = new Vector3(0, 1.8 + $scale * 0.9, -0.25);
		}

		$this->seatPos = new Vector3(0, $scale * 0.4 - 0.3, 0);
		$this->networkPropertiesDirty = true;
	}

	public static function getNetworkTypeId(): string {
		return static::NETWORK_ORIG_ID;
	}

	protected function getInitialSizeInfo(): EntitySizeInfo {
		return new EntitySizeInfo($this->height, $this->width);
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
	 */
	public function getEntityType(): string {
		return strtr($this->getName(), [
			" "   => "",
			"Pet" => ""
		]);
	}

	/**
	 * Returns the name of the pet type.
	 */
	public function getName(): string {
		return $this->name;
	}

	public function useProperties(array $properties): void {
		$this->speed = (float) $properties["Speed"];
		$this->canBeRidden = (bool) $properties["Can-Be-Ridden"];
		$this->canBeChested = (bool) $properties["Can-Be-Chested"];
		$this->canAttack = (bool) $properties["Can-Attack"];
		$this->canRide = (bool) $properties["Can-Sit-On-Owner"];
		$this->maxSize = (float) $properties["Max-Size"];
	}

	public function getPetData(): PetData {
		return new PetData(
			$this->getEntityType(),
			$this->getPetOwnerName(),
			$this->petName,
			$this->getStartingScale(),
			$this->baby,
			$this->petLevel,
			$this->petLevelPoints,
			$this->chested,
			$this->visibility,
			$this->inventory_manager->compressContents()
		);
	}

	public function isRiding(): bool {
		return $this->riding;
	}

	public function setRiding(bool $riding): void {
		$this->riding = $riding;
		$this->networkPropertiesDirty = true;
	}

	public function isChested(): bool {
		return $this->chested;
	}

	public function setChested(bool $value = true): void {
		if($this->isChested() !== $value) {
			$this->chested = $value;
			$loader = $this->getLoader();
			if($loader->getBlockPetsConfig()->storeToDatabase()) {
				$loader->getDatabase()->updateChested($this);
			}
			$this->networkPropertiesDirty = true;
		}
	}

	public function isBaby(): bool {
		return $this->baby;
	}

	public function setBaby(bool $baby): void {
		$this->baby = $baby;
		$this->networkPropertiesDirty = true;
	}

	public function isSaddled(): bool {
		return $this->saddled;
	}

	public function setSaddled(bool $saddled): void {
		$this->saddled = $saddled;
		$this->networkPropertiesDirty = true;
	}

	public function getVisibility(): bool {
		return $this->visibility;
	}

	/**
	 * @internal
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
		$player->getNetworkSession()->sendDataPacket(AddActorPacket::create(
			$this->getId(),
			$this->getId(),
			static::getNetworkTypeId(),
			$this->location->asVector3(),
			$this->getMotion(),
			$this->location->pitch,
			$this->location->yaw,
			$this->location->yaw,
			array_map(static function(Attribute $attr): NetworkAttribute {
				return new NetworkAttribute($attr->getId(), $attr->getMinValue(), $attr->getMaxValue(), $attr->getValue(), $attr->getDefaultValue());
			}, $this->attributeMap->getAll()),
			$this->getAllNetworkData(),
			array_values($this->links)
		));
	}

	/**
	 * Returns the player that owns this pet if they are online.
	 */
	final public function getPetOwner(): ?Player {
		return $this->petOwner;
	}

	/**
	 * Returns the actual name of the pet. Not to be confused with getName(), which returns the pet type name.
	 */
	public function getPetName(): string {
		return $this->petName;
	}

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
					$this->getWorld()->addParticle($this->location->add(0, 2, 0), new HeartParticle(4));

					if($this->getLoader()->getBlockPetsConfig()->giveExperienceWhenFed()) {
						$this->addPetLevelPoints((int) ($nutrition / 40 * LevelCalculator::getRequiredLevelPoints($this->getPetLevel())));
					}

					$this->calculator->updateNameTag();
					$source->cancel();
				} elseif($hand->getId() === ItemIds::CHEST && $this->canBeChested) {
					if(!$this->isChested() && $this->getPetOwnerName() === $player->getName()) {
						$ev = new PetInventoryInitializationEvent($this->getLoader(), $this);
						$ev->call();
						if(!$ev->isCancelled()) {
							$hand->pop();
							$player->getInventory()->setItemInHand($hand);
							$this->setChested();
							$source->cancel();
						}
					}
				} elseif($player->getName() === $this->getPetOwnerName()) {
					if($this->isChested() && $hand->getId() === ItemIds::AIR) {
						$source->cancel();
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
	 * Levels up the pet's experience level by the given amount. Sends a title if $silent is false or not set.
	 */
	public function levelUp(int $amount = 1, bool $silent = false): bool {
		if($amount < 1) {
			return false;
		}

		$ev = new PetLevelUpEvent($this->getLoader(), $this, $this->getPetLevel(), $this->getPetLevel() + $amount);
		$ev->call();
		if($ev->isCancelled()) {
			return false;
		}
		$this->setPetLevel($ev->getTo());

		if(!$silent && $this->getPetOwner() !== null) {
			$this->getPetOwner()->sendTitle((TextFormat::GREEN . "Level Up!"), (TextFormat::AQUA . "Your pet " . $this->getPetName() . TextFormat::RESET . TextFormat::AQUA . " turned level " . $ev->getTo() . "!"));
		}
		return true;
	}

	/**
	 * Adds the given amount of experience points to the pet. Levels up the pet if required.
	 */
	public function addPetLevelPoints(int $points): bool {
		$this->levelUp(LevelCalculator::calculateLevelUp($points, $this->getPetLevel(), $remaining));
		$this->setPetLevelPoints($remaining);
		$this->calculator->updateNameTag();
		return true;
	}

	/**
	 * Returns the current experience level of the pet.
	 */
	public function getPetLevel(): int {
		return $this->petLevel;
	}

	/**
	 * Sets the pet's experience level to the given amount.
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
	 * Returns the pet's current experience level points.
	 */
	public function getPetLevelPoints(): int {
		return $this->petLevelPoints;
	}

	/**
	 * Sets the pet's experience level points to the given amount.
	 */
	public function setPetLevelPoints(int $points): void {
		$this->petLevelPoints = $points;
		$loader = $this->getLoader();
		if($loader->getBlockPetsConfig()->storeToDatabase()) {
			$loader->getDatabase()->updateExperience($this);
		}
	}

	/**
	 * Returns the name of the owner of this pet.
	 */
	final public function getPetOwnerName(): string {
		return $this->petOwner->getName();
	}

	/**
	 * Returns the inventory of this pet.
	 */
	public function getInventory(): Inventory {
		return $this->inventory_manager->getInventory();
	}

	/**
	 * Returns the inventory manager of this pet.
	 */
	public function getInventoryManager(): PetInventoryManager {
		return $this->inventory_manager;
	}

	/**
	 * Internal.
	 */
	public function getNameTag(): string {
		return $this->getPetName();
	}

	protected function syncNetworkData(EntityMetadataCollection $properties): void {
		parent::syncNetworkData($properties);

		$properties->setGenericFlag(EntityMetadataFlags::CHESTED, $this->chested);
		$properties->setGenericFlag(EntityMetadataFlags::BABY, $this->baby);
		$properties->setGenericFlag(EntityMetadataFlags::TAMED, true);
		$properties->setGenericFlag(EntityMetadataFlags::RIDING, $this->riding);
		$properties->setGenericFlag(EntityMetadataFlags::SADDLED, $this->saddled);
	}

	public function generateCustomPetData(): void {
	}

	/**
	 * Returns the network (entity) ID of the entity.
	 */
	final public function getNetworkId(): int {
		return static::NETWORK_ID;
	}

	/**
	 * Returns the speed of this pet.
	 */
	public function getSpeed(): float {
		return $this->speed;
	}

	public function getStartingScale(): float {
		return $this->scale;
	}

	/**
	 * Returns the attack damage of this pet.
	 */
	public function getAttackDamage(): int {
		return $this->attackDamage;
	}

	/**
	 * Sets the attack damage to the given amount.
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

	protected function broadcastMovement(bool $teleport = false): void {
		if($this->isRiding()) {
			return;
		}

		parent::broadcastMovement($teleport);
	}

	protected function onDeath(): void {
		parent::onDeath();
		$this->dead = true;

		$loader = $this->getLoader();
		$loader->getDatabase()->unregisterPet($this);
		$loader->removePet($this, false); // don't close since it will be closed automatically

		$ev = new PetRespawnEvent($loader, $this->getPetData(), $loader->getBlockPetsConfig()->getRespawnTime());
		$ev->call();

		if($ev->isCancelled()) {
			$this->flagForDespawn();
			return;
		}

		$newPet = $loader->clonePet($ev->getPetData());
		if($newPet === null) {
			return;
		}

		$newPet->register();
		$newPet->despawnFromAll();
		$newPet->setDormant();

		$loader->getScheduler()->scheduleDelayedTask(new PetRespawnTask($loader, $newPet), $ev->getDelay() * 20);
	}

	final public function onUpdate(int $currentTick): bool {
		if(!parent::onUpdate($currentTick) && $this->isClosed()) {
			return false;
		}
		if($this->dead) {
			$this->close();
			return false;
		}
		$petOwner = $this->getPetOwner();
		if($petOwner !== null && $this->isRiding()) {
			$this->gravityEnabled = false;

			$ownerLoc = $petOwner->getLocation();
			$currLoc = $this->getLocation();

			$x = $ownerLoc->getX() - $currLoc->getX();
			$y = $ownerLoc->getY() - $currLoc->getY();
			$z = $ownerLoc->getZ() - $currLoc->getZ();

			if($x !== 0.0 || $z !== 0.0 || $y !== -$petOwner->getSize()->getHeight()) {
				$this->move($x, $y + $petOwner->getSize()->getHeight(), $z);
			}
			return false;
		}
		$this->gravityEnabled = true;
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
			if(!$this->isDormant() && ($this->getWorld()->getEntity($petOwner->getId()) === null || $this->location->distance($petOwner->location) >= 50)) {
				$this->teleport($petOwner->location);
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

	public function shouldFindNewPosition(): bool {
		if($this->positionSeekTick >= 60) {
			$this->positionSeekTick = 0;
			return true;
		}
		return false;
	}

	/**
	 * Detaches the rider from the pet.
	 */
	public function throwRiderOff(): bool {
		if(!$this->isRidden()) {
			return false;
		}

		$rider = $this->getRider();
		$this->rider = null;
		$rider->canCollide = true;
		$this->removeLink($rider, self::LINK_RIDER);

		$this->riding = false;
		$this->networkPropertiesDirty = true;

		if($rider->isSurvival()) {
			$rider->setAllowFlight(false);
		}

		$rider->onGround = true;

		$this->size = $this->getInitialSizeInfo();
		$this->recalculateBoundingBox();

		return true;
	}

	/**
	 * Returns the rider of the pet if it has a rider, and null if this is not the case.
	 */
	public function getRider(): ?Player {
		return $this->rider;
	}

	/**
	 * Sets the given player as rider on the pet, connecting it to it and initializing some things.
	 */
	public function setRider(Player $player): bool {
		if($this->isRidden()) {
			return false;
		}

		$this->rider = $player;
		$player->canCollide = false;
		$owner = $this->getPetOwner();

		$player->getNetworkProperties()->setVector3(EntityMetadataProperties::RIDER_SEAT_POSITION, $this->riderSeatPos);
		$player->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::RIDING, true);

		$this->addLink($player, self::LINK_RIDER);

		$this->saddled = true;
		$this->networkPropertiesDirty = true;

		if($owner->isSurvival()) {
			$owner->setAllowFlight(true); // Set allow flight to true to prevent any 'kicked for flying' issues.
		}

		// adding more vertical area to the BB, so the horizontal can just be the maximum.
		$this->size = new EntitySizeInfo(
			max(($this->riderSeatPos->y / 2.5) + $player->size->getHeight(), $this->size->getHeight()),
			max($player->size->getWidth(), $this->size->getWidth())
		);

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
	 */
	public function getCalculator(): Calculator {
		return $this->calculator;
	}

	public abstract function doRidingMovement(float $motionX, float $motionZ): void;

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
			$this->flagForDespawn();
			$this->setDormant();
			return false;
		}
		if(!$this->getPetOwner()->isAlive()) {
			return false;
		}
		return true;
	}

	/**
	 * Returns whether this pet is being ridden or not.
	 */
	public function isRidden(): bool {
		return $this->rider !== null;
	}

	/**
	 * Returns whether this pet is dormant or not. If this pet is dormant, it will not move.
	 */
	public function isDormant(): bool {
		return $this->dormant;
	}

	/**
	 * Sets the dormant state to this pet with the given value.
	 */
	public function setDormant(bool $value = true): void {
		$this->dormant = $value;
	}

	/**
	 * Adds a link to this pet.
	 */
	public function addLink(Entity $entity, int $type): void {
		$this->removeLink($entity, $type);
		$viewers = $this->getViewers();

		switch($type) {
			case self::LINK_RIDER:
				$link = new EntityLink($this->getId(), $entity->getId(), self::STATE_SITTING, true, true);

				if($entity instanceof Player) {
					$pk = new SetActorLinkPacket();
					$pk->link = $link;
					$entity->getNetworkSession()->sendDataPacket($pk);

					$link_2 = new EntityLink($this->getId(), 0, self::STATE_SITTING, true, true);

					$pk = new SetActorLinkPacket();
					$pk->link = $link_2;
					$entity->getNetworkSession()->sendDataPacket($pk);
					unset($viewers[$entity->getId()]);
				}
				break;
			case self::LINK_RIDING:
				$link = new EntityLink($entity->getId(), $this->getId(), self::STATE_SITTING, true, false);

				if($entity instanceof Player) {
					$pk = new SetActorLinkPacket();
					$pk->link = $link;
					$entity->getNetworkSession()->sendDataPacket($pk);

					$link_2 = new EntityLink($entity->getId(), 0, self::STATE_SITTING, true, false);

					$pk = new SetActorLinkPacket();
					$pk->link = $link_2;
					$entity->getNetworkSession()->sendDataPacket($pk);
					unset($viewers[$entity->getId()]);
				}
				break;
			default:
				throw new \InvalidArgumentException();
		}

		if(!empty($viewers)) {
			$pk = new SetActorLinkPacket();
			$pk->link = $link;
			$this->server->broadcastPackets($viewers, [$pk]);
		}

		$this->links[$type] = $link;
	}

	/**
	 * Removes a link from this pet.
	 */
	public function removeLink(Entity $entity, int $type): void {
		if(!isset($this->links[$type])) {
			return;
		}

		$viewers = $this->getViewers();

		switch($type) {
			case self::LINK_RIDER:
				$link = new EntityLink($this->getId(), $entity->getId(), self::STATE_STANDING, true, true);

				if($entity instanceof Player) {
					$pk = new SetActorLinkPacket();
					$pk->link = $link;
					$entity->getNetworkSession()->sendDataPacket($pk);

					$link_2 = new EntityLink($this->getId(), 0, self::STATE_STANDING, true, true);

					$pk = new SetActorLinkPacket();
					$pk->link = $link_2;
					$entity->getNetworkSession()->sendDataPacket($pk);
					unset($viewers[$entity->getId()]);
				}
				break;
			case self::LINK_RIDING:
				$link = new EntityLink($entity->getId(), $this->getId(), self::STATE_STANDING, true, false);

				if($entity instanceof Player) {
					$pk = new SetActorLinkPacket();
					$pk->link = $link;
					$entity->getNetworkSession()->sendDataPacket($pk);

					$link_2 = new EntityLink($entity->getId(), 0, self::STATE_STANDING, true, false);

					$pk = new SetActorLinkPacket();
					$pk->link = $link_2;
					$entity->getNetworkSession()->sendDataPacket($pk);
					unset($viewers[$entity->getId()]);
				}
				break;
			default:
				throw new \InvalidArgumentException();
		}

		unset($this->links[$type]);

		if(!empty($viewers)) {
			$pk = new SetActorLinkPacket();
			$pk->link = $link;
			$this->server->broadcastPackets($viewers, [$pk]);
		}
	}

	public function sitOnOwner(): bool {
		if($this->riding) {
			return false;
		}

		$this->riding = true;
		$this->saddled = false;
		$this->getNetworkProperties()->setVector3(EntityMetadataProperties::RIDER_SEAT_POSITION, $this->seatPos);
		$this->networkPropertiesDirty = true;
		$this->addLink($this->getPetOwner(), self::LINK_RIDING);

		return true;
	}

	public function dismountFromOwner(): bool {
		if(!$this->riding) {
			return false;
		}
		$this->riding = false;
		$this->networkPropertiesDirty = true;
		$petOwner = $this->getPetOwner();
		$this->removeLink($petOwner, self::LINK_RIDING);
		$this->teleport($petOwner->location);
		return true;
	}

	public function getMaxSize(): float {
		return $this->maxSize;
	}
}
