<?php

namespace BlockHorizons\BlockPets\pets;

use BlockHorizons\BlockPets\events\PetLevelUpEvent;
use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\creatures\EnderDragonPet;
use pocketmine\entity\Creature;
use pocketmine\entity\Rideable;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\item\Food;
use pocketmine\level\Level;
use pocketmine\level\particle\HeartParticle;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\network\mcpe\protocol\SetEntityLinkPacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

abstract class BasePet extends Creature implements Rideable {

	const STATE_SITTING = 2;
	const STATE_STANDING = 3;

	const TIER_COMMON = 1;
	const TIER_UNCOMMON = 2;
	const TIER_SPECIAL = 3;
	const TIER_EPIC = 4;
	const TIER_LEGENDARY = 5;

	public $name;
	public $speed = 1.0;
	public $scale = 1.0;
	public $networkId;

	protected $tier = self::TIER_COMMON;
	protected $petOwner;
	protected $petLevel = 0;
	protected $petName = "";
	protected $ridden = false;
	protected $rider = null;
	protected $attackDamage = 0;
	protected $petLevelPoints = 0;
	protected $calculator;

	private $dormant = false;

	public function __construct(Level $level, CompoundTag $nbt) {
		parent::__construct($level, $nbt);
		$this->calculator = new Calculator($this);

		$this->setNameTagVisible(true);
		$this->setNameTagAlwaysVisible(true);

		$this->petLevel = $this->namedtag["petLevel"] - 1;
		$this->petOwner = $this->namedtag["petOwner"];
		$this->scale = $this->namedtag["scale"];
		$this->petName = $this->namedtag["petName"];
		$this->petLevelPoints = $this->namedtag["petLevelPoints"];

		$this->setScale($this->scale);
		$this->generateCustomPetData();

		$this->levelUp(1, true);
		$this->spawnToAll();
	}

	public function generateCustomPetData() {

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
	 * Returns the BlockPets Loader. Only for internal usage.
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
		return $this->getLevel()->getServer()->getPlayer($this->petOwner);
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
				$food = $player->getInventory()->getItemInHand();
				if($this->getHealth() === $this->getMaxHealth()) {
					parent::attack($damage, $source);
					return;
				}
				if($food instanceof Food) {
					$nutrition = $food->getFoodRestore();
					$heal = $nutrition / 20 * $this->getMaxHealth();
					if($this->getHealth() + $heal > $this->getMaxHealth()) {
						$heal = $this->getMaxHealth() - $this->getHealth();
					}
					$remainder = $player->getInventory()->getItemInHand();
					$remainder->setCount($remainder->getCount() - 1);
					$player->getInventory()->setItemInHand($remainder);
					$this->heal($heal, new EntityRegainHealthEvent($this, $heal, EntityRegainHealthEvent::CAUSE_SATURATION));
					$this->getLevel()->addParticle(new HeartParticle($this->add(0, 2), 4));

					$this->addPetLevelPoints($nutrition / 20 * $this->getRequiredLevelPoints($this->getPetLevel()) + 2);
					$this->calculator->updateNameTag();
					$source->setCancelled();
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
			$this->calculator->recalculateAll();
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
	 * Internal.
	 *
	 * @return string
	 */
	public function getNameTag(): string {
		return $this->getPetName();
	}

	/**
	 * Returns the tier of the pet.
	 *
	 * @return int
	 */
	public function getTier(): int {
		return $this->tier;
	}

	public function initEntity() {
		parent::initEntity();
		$this->setDataProperty(self::DATA_FLAG_NO_AI, self::DATA_TYPE_BYTE, 1);
		$this->setDataFlag(self::DATA_FLAG_BABY, self::DATA_TYPE_BYTE, (int) $this->namedtag["isBaby"]);
		if((bool) $this->namedtag["isBaby"]) {
			$this->setScale($this->getStartingScale() / 2);
		}
	}

	/**
	 * @return float
	 */
	public function getStartingScale(): float {
		return $this->scale;
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
		$this->namedtag->petOwner = new StringTag("petOwner", $this->getPetOwnerName());
		$this->namedtag->petName = new StringTag("petName", $this->getPetName());
		$this->namedtag->speed = new FloatTag("speed", $this->getSpeed());
		$this->namedtag->scale = new FloatTag("scale", $this->getStartingScale());
		$this->namedtag->networkId = new IntTag("networkId", $this->getNetworkId());
		$this->namedtag->petLevel = new IntTag("petLevel", $this->getPetLevel());
		$this->namedtag->petLevelPoints = new IntTag("petLevelPoints", (int) $this->getPetLevelPoints());
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
	 * Returns the speed of this pet.
	 *
	 * @return float
	 */
	public function getSpeed(): float {
		return $this->speed;
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
		$this->setDataFlag(self::DATA_FLAG_SADDLED, self::DATA_TYPE_BYTE, 1);

		$pk = new SetEntityLinkPacket();
		$pk->to = $player->getId();
		$pk->from = $this->getId();
		$pk->type = self::STATE_SITTING;
		$this->server->broadcastPacket($this->level->getPlayers(), $pk);

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
	 * Returns whether this pet is being ridden or not.
	 *
	 * @return bool
	 */
	public function isRidden(): bool {
		return $this->ridden;
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
	 * @param $currentTick
	 *
	 * @return bool
	 */
	public function onUpdate($currentTick) {
		$petOwner = $this->getPetOwner();
		if($this->closed || !$this->isAlive()) {
			return false;
		}
		if(mt_rand() === 7) {
			if($this->getHealth() !== $this->getMaxHealth()) {
				$this->heal(1, new EntityRegainHealthEvent($this, 1, EntityRegainHealthEvent::CAUSE_REGEN));
				$this->calculator->updateNameTag();
			}
		}
		if($this->isDormant()) {
			$this->despawnFromAll();
			return false;
		}
		if($petOwner === null) {
			$this->ridden = false;
			$this->rider = null;
			$this->despawnFromAll();
			$this->setDormant();
			return false;
		}
		if($this->getLevel()->getId() !== $petOwner->getLevel()->getId()) {
			$this->getLoader()->createPet($this->getEntityType(), $this->getPetOwner(), $this->getPetName(), $this->getStartingScale(), $this->namedtag["isBaby"], $this->getPetLevel(), $this->getPetLevelPoints());
			$this->close();
			return true;
		}
		if($this->distance($petOwner) >= 50) {
			$this->teleport($petOwner);
			return true;
		}
		$this->updateMovement();
		parent::onUpdate($currentTick);
		return true;
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
	 * Detaches the rider from the pet.
	 */
	public function throwRiderOff() {
		$pk = new SetEntityLinkPacket();
		$pk->from = $this->getId();
		$pk->to = $this->getPetOwner()->getId();
		$pk->type = self::STATE_STANDING;
		$this->ridden = false;
		$this->rider = null;
		$this->getPetOwner()->canCollide = true;
		$this->server->broadcastPacket($this->level->getPlayers(), $pk);

		$pk = new SetEntityLinkPacket();
		$pk->from = $this->getPetOwner()->getId();
		$pk->to = 0;
		$pk->type = self::STATE_STANDING;
		$this->getPetOwner()->dataPacket($pk);
		$this->setDataFlag(self::DATA_FLAG_SADDLED, self::DATA_TYPE_BYTE, 0);

		if($this->getPetOwner()->isSurvival()) {
			$this->getPetOwner()->setAllowFlight(false);
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
	 * @param $motionX
	 * @param $motionZ
	 *
	 * @return mixed
	 */
	public abstract function doRidingMovement($motionX, $motionZ);
}
