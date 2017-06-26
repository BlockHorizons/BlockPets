<?php

namespace BlockHorizons\BlockPets\pets;

use BlockHorizons\BlockPets\events\PetInventoryInitializationEvent;
use BlockHorizons\BlockPets\events\PetLevelUpEvent;
use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\creatures\ArrowPet;
use BlockHorizons\BlockPets\pets\creatures\EnderDragonPet;
use BlockHorizons\BlockPets\pets\inventory\PetInventoryHolder;
use pocketmine\entity\Creature;
use pocketmine\entity\Rideable;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\item\Food;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\particle\HeartParticle;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\network\mcpe\protocol\SetEntityLinkPacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

abstract class BasePet extends Creature implements Rideable {

	const STATE_SITTING = 1;
	const STATE_STANDING = 0;

	const TIER_COMMON = 1;
	const TIER_UNCOMMON = 2;
	const TIER_SPECIAL = 3;
	const TIER_EPIC = 4;
	const TIER_LEGENDARY = 5;

	public $name;
	public $scale = 1.0;
	public $networkId;

	protected $petOwner;
	protected $petLevel = 0;
	protected $petName = "";
	protected $ridden = false;
	/** @var null|string */
	protected $rider = null;

	protected $attackDamage = 4;
	protected $speed = 1.0;
	protected $petLevelPoints = 0;

	protected $canBeRidden = true;
	protected $canBeChested = true;
	protected $canAttack = true;

	protected $calculator;

	protected $xOffset = 0;
	protected $yOffset = 0;
	protected $zOffset = 0;

	private $dormant = false;
	private $chested = false;
	private $shouldIgnoreEvent = false;
	private $positionSeekTick = 60;
	private $inventory = null;

	public function __construct(Level $level, CompoundTag $nbt) {
		parent::__construct($level, $nbt);
		$this->selectProperties();
		$this->calculator = new Calculator($this);

		$this->setNameTagVisible(true);
		$this->setNameTagAlwaysVisible(true);

		$this->petLevel = $this->namedtag["petLevel"] - 1;
		$this->petOwner = $this->namedtag["petOwner"];
		$this->scale = $this->namedtag["scale"];
		$this->petName = $this->namedtag["petName"];
		$this->petLevelPoints = $this->namedtag["petLevelPoints"];
		$this->chested = (bool) $this->namedtag["chested"];

		$this->setScale($this->scale);
		if((bool) $this->namedtag["isBaby"] === true) {
			$this->setScale(0.5);
		}
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_CHESTED, (bool) $this->isChested());

		$this->inventory = new PetInventoryHolder($this);
		$this->levelUp(1, true);
		$this->spawnToAll();
	}

	public function selectProperties() {
		$properties = $this->getLoader()->getPetProperties()->getPropertiesFor($this->getEntityType());
		$this->useProperties($properties);
	}

	/**
	 * Returns the BlockPets Loader. For internal usage.
	 *
	 * @return Loader
	 */
	public function getLoader(): Loader {
		$plugin = $this->getLevel()->getServer()->getPluginManager()->getPlugin("BlockPets");
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
	public function useProperties(array $properties) {
		$this->speed = (float) $properties["Speed"];
		$this->canBeRidden = (bool) $properties["Can-Be-Ridden"];
		$this->canBeChested = (bool) $properties["Can-Be-Chested"];
		$this->canAttack = (bool) $properties["Can-Attack"];
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
	public function setChested(bool $value = true) {
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_CHESTED, $value);
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
		$this->getLoader()->getServer()->getPluginManager()->callEvent($ev = new PetLevelUpEvent($this->getLoader(), $this, $this->getPetLevel(), $this->getPetLevel() + $amount));
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
	public function setPetLevel(int $petLevel) {
		$this->petLevel = $petLevel;
	}

	/**
	 * Returns the player that owns this pet if they are online, and null if not.
	 *
	 * @return Player|null
	 */
	public function getPetOwner() {
		return $this->getLoader()->getServer()->getPlayer($this->petOwner);
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
	public function attack($damage, EntityDamageEvent $source) {
		if($source instanceof EntityDamageByEntityEvent) {
			$player = $source->getDamager();
			if($player instanceof Player) {
				$hand = $player->getInventory()->getItemInHand();
				if($hand instanceof Food) {
					if($this->getHealth() === $this->getMaxHealth()) {
						parent::attack($damage, $source);
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
					$this->heal($heal, new EntityRegainHealthEvent($this, $heal, EntityRegainHealthEvent::CAUSE_SATURATION));
					$this->getLevel()->addParticle(new HeartParticle($this->add(0, 2), 4));

					if($this->getLoader()->getBlockPetsConfig()->giveExperienceWhenFed()) {
						$this->addPetLevelPoints($nutrition / 40 * $this->getRequiredLevelPoints($this->getPetLevel()));
					}

					$this->calculator->updateNameTag();
					$source->setCancelled();

				} elseif($hand->getId() === Item::CHEST && $this->canBeChested) {
					if(!$this->isChested() && $this->getPetOwnerName() === $player->getName()) {
						$this->getLoader()->getServer()->getPluginManager()->callEvent($ev = new PetInventoryInitializationEvent($this->getLoader(), $this));
						if(!$ev->isCancelled()) {
							$remainder = $hand;
							$remainder->setCount($remainder->getCount() - 1);
							$player->getInventory()->setItemInHand($remainder);
							$this->setChested();
							$source->setCancelled();
						}
					}

				} elseif($player->getName() === $this->getPetOwnerName()) {
					if($this->isChested() && $player->getInventory()->getItemInHand()->getId() === Item::AIR) {
						$source->setCancelled();
						$this->getInventory()->openToOwner();
					}
				}
			}
		}
		$this->calculator->updateNameTag();
		parent::attack($damage, $source);
	}

	/**
	 * Adds the given amount of experience points to the pet. Levels up the pet if required.
	 *
	 * @param float $points
	 *
	 * @return bool
	 */
	public function addPetLevelPoints(float $points): bool {
		$totalPoints = $this->getPetLevelPoints() + $points;
		if($totalPoints >= $this->getRequiredLevelPoints($this->getPetLevel())) {
			$this->setPetLevelPoints($totalPoints - $this->getRequiredLevelPoints($this->getPetLevel()));
			$this->levelUp();
			return true;
		} else {
			$this->setPetLevelPoints($totalPoints);
			$this->calculator->updateNameTag();
			return false;
		}
	}

	/**
	 * Returns the pet's current experience level points.
	 *
	 * @return float
	 */
	public function getPetLevelPoints(): float {
		return $this->petLevelPoints;
	}

	/**
	 * Sets the pet's experience level points to the given amount.
	 *
	 * @param int $points
	 */
	public function setPetLevelPoints(float $points) {
		$this->petLevelPoints = $points;
	}

	/**
	 * Returns the required amount of points for the given level to level up automatically.
	 *
	 * @param int $level
	 *
	 * @return int
	 */
	public function getRequiredLevelPoints(int $level) {
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

	public function initEntity() {
		parent::initEntity();
		$this->generateCustomPetData();
		$this->setDataProperty(self::DATA_FLAG_NO_AI, self::DATA_TYPE_BYTE, 1);
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_BABY, (bool) $this->namedtag["isBaby"]);
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_TAMED, true);
	}

	public function generateCustomPetData() {

	}

	/**
	 * @param Player $player
	 */
	public function spawnTo(Player $player) {
		parent::spawnTo($player);
		$pk = new AddEntityPacket();
		$pk->entityRuntimeId = $this->getId();
		$pk->type = $this->getNetworkId();
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->speedX = $pk->speedY = $pk->speedZ = 0.0;
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk);
	}

	/**
	 * Returns the network (entity) ID of the entity.
	 *
	 * @return int
	 */
	public function getNetworkId(): int {
		return $this->networkId;
	}

	public function saveNBT() {
		parent::saveNBT();
		$this->namedtag->petOwner = new StringTag("petOwner", (string) $this->getPetOwnerName());
		$this->namedtag->petName = new StringTag("petName", (string) $this->getPetName());
		$this->namedtag->speed = new FloatTag("speed", (float) $this->getSpeed());
		$this->namedtag->scale = new FloatTag("scale", (float) $this->getStartingScale());
		$this->namedtag->networkId = new IntTag("networkId", (int) $this->getNetworkId());
		$this->namedtag->petLevel = new IntTag("petLevel", (int) $this->getPetLevel());
		$this->namedtag->petLevelPoints = new IntTag("petLevelPoints", (int) $this->getPetLevelPoints());
		$this->namedtag->chested = new ByteTag("chested", (int) $this->isChested());
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
	public function setAttackDamage(int $amount) {
		$this->attackDamage = $amount;
	}

	/**
	 * @param $currentTick
	 *
	 * @return bool
	 */
	public function onUpdate($currentTick) {
		$petOwner = $this->getPetOwner();
		if(mt_rand(1, 60) === 1) {
			if($this->getHealth() !== $this->getMaxHealth()) {
				$this->heal(1, new EntityRegainHealthEvent($this, 1, EntityRegainHealthEvent::CAUSE_REGEN));
				$this->calculator->updateNameTag();
			}
		}
		if($this->getLevel()->getId() !== $petOwner->getLevel()->getId()) {
			$this->getLoader()->createPet($this->getEntityType(), $petOwner, $this->getPetName(), $this->getStartingScale(), $this->namedtag["isBaby"], $this->getPetLevel(), $this->getPetLevelPoints(), $this->isChested());
			$this->close();
			return true;
		}
		if($this->distance($petOwner) >= 50) {
			$this->teleport($petOwner);
			return true;
		}
		$this->positionSeekTick++;
		if($this->shouldFindNewPosition()) {
			if(!$this->getLoader()->getBlockPetsConfig()->shouldStalkPetOwner()) {
				if(rand(0, 1) === 1) {
					$multiplicationValue = 1;
				} else {
					$multiplicationValue = -1;
				}
				$this->xOffset = lcg_value() * $multiplicationValue * (3 + $this->getScale());
				$this->yOffset = lcg_value() * $multiplicationValue * (3 + $this->getScale());
				$this->zOffset = lcg_value() * $multiplicationValue * (3 + $this->getScale());
			}
		}
		$this->updateMovement();
		parent::onUpdate($currentTick);
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

	public function kill($ignore = false) {
		$this->shouldIgnoreEvent = $ignore;
		parent::kill();
	}

	/**
	 * Detaches the rider from the pet.
	 */
	public function throwRiderOff() {
		$pk = new SetEntityLinkPacket();
		$pk->from = $this->getId();
		$pk->to = $this->getPetOwner()->getId();
		$pk->type = self::STATE_STANDING;
		$this->ridden = false;
		$rider = $this->getRider();
		$this->rider = null;
		$this->getPetOwner()->canCollide = true;
		$this->server->broadcastPacket($this->level->getPlayers(), $pk);

		$pk = new SetEntityLinkPacket();
		$pk->from = $this->getPetOwner()->getId();
		$pk->to = 0;
		$pk->type = self::STATE_STANDING;
		$this->getPetOwner()->dataPacket($pk);
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_SADDLED, false);
		if($this->getPetOwner() !== null) {
			$rider->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_RIDING, false);
			if($this->getPetOwner()->isSurvival()) {
				$rider->setAllowFlight(false);
			}
		}
	}

	/**
	 * Returns the rider of the pet if it has a rider, and null if this is not the case.
	 *
	 * @return Player|null
	 */
	public function getRider() {
		return $this->getLevel()->getServer()->getPlayer($this->rider);
	}

	/**
	 * Sets the given player as rider on the pet, connecting it to it and initializing some things.
	 *
	 * @param Player $player
	 */
	public function setRider(Player $player) {
		$this->ridden = true;
		$this->rider = $player->getName();
		$player->canCollide = false;
		$this->getPetOwner()->setDataProperty(self::DATA_RIDER_SEAT_POSITION, self::DATA_TYPE_VECTOR3F, [0, 1.8 + $this->getScale() * 0.9, -0.25]);
		if($this instanceof EnderDragonPet) {
			$this->getPetOwner()->setDataProperty(self::DATA_RIDER_SEAT_POSITION, self::DATA_TYPE_VECTOR3F, [0, 2.65 + $this->getScale(), -1.7]);
		} elseif($this instanceof SmallCreature) {
			$this->getPetOwner()->setDataProperty(self::DATA_RIDER_SEAT_POSITION, self::DATA_TYPE_VECTOR3F, [0, 0.78 + $this->getScale() * 0.9, -0.25]);
		}
		$player->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_RIDING, true);
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_SADDLED, true);

		$pk = new SetEntityLinkPacket();
		$pk->to = $player->getId();
		$pk->from = $this->getId();
		$pk->type = self::STATE_SITTING;
		$this->server->broadcastPacket($this->server->getOnlinePlayers(), $pk);

		$pk = new SetEntityLinkPacket();
		$pk->to = 0;
		$pk->from = $this->getId();
		$pk->type = self::STATE_SITTING;
		$player->dataPacket($pk);

		if($this->getPetOwner()->isSurvival()) {
			$this->getPetOwner()->setAllowFlight(true); // Set allow flight to true to prevent any 'kicked for flying' issues.
		}
	}

	/**
	 * Heals the current pet back to full health.
	 */
	public function fullHeal() {
		$diff = $this->getMaxHealth() - $this->getHealth();
		$this->heal($diff, new EntityRegainHealthEvent($this, $diff, EntityRegainHealthEvent::CAUSE_CUSTOM));
	}

	/**
	 * @param string $newName
	 */
	public function changeName(string $newName) {
		$this->getLoader()->getDatabase()->unregisterPet($this->getPetName(), $this->getPetOwner());
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
	 * @param $motionX
	 * @param $motionZ
	 *
	 * @return mixed
	 */
	public abstract function doRidingMovement($motionX, $motionZ);

	/**
	 * @return bool
	 */
	protected function checkUpdateRequirements(): bool {
		if($this->closed || $this->isRidden()) {
			return false;
		}
		if(!$this->isAlive()) {
			// All entities except players get closed automatically. No need to close it manually.
			$this->getLoader()->getDatabase()->unregisterPet($this->getPetName(), $this->getPetOwnerName());

			return true;
		}
		if($this->isDormant())  {
			$this->despawnFromAll();
			return false;
		}
		if($this->getPetOwner() === null) {
			$this->ridden = false;
			$this->rider = null;
			$this->despawnFromAll();
			$this->setDormant();
			if($this->getLoader()->getBlockPetsConfig()->fetchFromDatabase()) {
				$this->getCalculator()->storeToDatabase();
				$this->close();
			}
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
	public function setDormant(bool $value = true) {
		$this->dormant = $value;
	}
}
