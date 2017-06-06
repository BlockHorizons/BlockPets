<?php

namespace BlockHorizons\BlockPets\pets;

use BlockHorizons\BlockPets\events\PetLevelUpEvent;
use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\creatures\EnderDragonPet;
use pocketmine\entity\Creature;
use pocketmine\entity\Rideable;
use pocketmine\level\Level;
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

	public function __construct(Level $level, CompoundTag $nbt) {
		parent::__construct($level, $nbt);

		$this->setNameTagVisible(true);
		$this->setNameTagAlwaysVisible(true);

		$this->petLevel = $this->namedtag["petLevel"] - 1;
		$this->petOwner = $this->namedtag["petOwner"];
		$this->scale = $this->namedtag["scale"];
		$this->petName = $this->namedtag["petName"];

		$this->setScale($this->scale);

		$this->levelUp();
		$this->spawnToAll();
	}

	/**
	 * @param int $amount
	 *
	 * @return bool
	 */
	public function levelUp(int $amount = 1): bool {
		$this->getLoader()->getServer()->getPluginManager()->callEvent($ev = new PetLevelUpEvent($this->getLoader(), $this, $this->getPetLevel(), $this->getPetLevel() + $amount));
		if($ev->isCancelled()) {
			return false;
		}
		$this->setPetLevel($ev->getTo());

		$this->setNameTag(
			$this->getPetName() . PHP_EOL .
			TextFormat::GRAY . "Lvl." . TextFormat::AQUA . $this->getPetLevel() . " " . TextFormat::GRAY . $this->getName()
		);
		$this->getPetOwner()->addTitle((TextFormat::GREEN . "Level Up!"), (TextFormat::AQUA . "Your pet " . $this->getPetName() . TextFormat::RESET . TextFormat::AQUA . " turned level " . $ev->getTo() . "!"));
		return true;
	}

	/**
	 * @return Loader
	 */
	protected function getLoader(): Loader {
		$plugin = $this->getLevel()->getServer()->getPluginManager()->getPlugin("BlockPets");
		if($plugin instanceof Loader) {
			return $plugin;
		}
		return null;
	}

	/**
	 * @return int
	 */
	public function getPetLevel(): int {
		return $this->petLevel;
	}

	/**
	 * @param int $petLevel
	 */
	public function setPetLevel(int $petLevel) {
		$this->petLevel = $petLevel;
	}

	/**
	 * @return string
	 */
	public function getPetName(): string {
		return $this->petName;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * @return Player|null
	 */
	public function getPetOwner() {
		return $this->getLevel()->getServer()->getPlayer($this->petOwner);
	}

	/**
	 * @return int
	 */
	public function getTier(): int {
		return $this->tier;
	}

	public function initEntity() {
		parent::initEntity();
		$this->setDataProperty(self::DATA_FLAG_NO_AI, self::DATA_TYPE_BYTE, true);
		$this->setDataFlag(self::DATA_FLAG_BABY, self::DATA_TYPE_BYTE, (bool) $this->namedtag["isBaby"]);
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
		$pk->speedX = $pk->speedY = $pk->speedZ = 0;
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk);
	}

	/**
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
	}

	/**
	 * @return string
	 */
	public function getPetOwnerName(): string {
		return $this->petOwner;
	}

	/**
	 * @return float
	 */
	public function getSpeed(): float {
		return $this->speed;
	}

	/**
	 * @return int
	 */
	public function getAttackDamage(): int {
		return $this->attackDamage;
	}

	/**
	 * @param int $amount
	 */
	public function setAttackDamage(int $amount) {
		$this->attackDamage = $amount;
	}

	/**
	 * @return Player|null
	 */
	public function getRider() {
		return $this->getLevel()->getServer()->getPlayer($this->rider);
	}

	/**
	 * @param Player $player
	 */
	public function setRider(Player $player) {
		$this->ridden = true;
		$this->rider = $player->getName();
		$player->canCollide = false;
		$this->getPetOwner()->setDataProperty(57, self::DATA_TYPE_VECTOR3F, [0, 1.8 + $this->getScale() * 0.9, -0.25]);
		if($this instanceof EnderDragonPet) {
			$this->getPetOwner()->setDataProperty(57, self::DATA_TYPE_VECTOR3F, [0, 2.65 + $this->getScale(), -1.7]);
		} elseif($this instanceof SmallCreature) {
			$this->getPetOwner()->setDataProperty(57, self::DATA_TYPE_VECTOR3F, [0, 0.78 + $this->getScale() * 0.9, -0.25]);
		}
		$this->setDataFlag(self::DATA_FLAG_SADDLED, self::DATA_TYPE_BYTE, true);

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
		$this->setDataFlag(self::DATA_FLAG_SADDLED, self::DATA_TYPE_BYTE, false);

		if($this->getPetOwner()->isSurvival()) {
			$this->getPetOwner()->setAllowFlight(false);
		}
	}

	/**
	 * @return bool
	 */
	public function isRidden(): bool {
		return $this->ridden;
	}

	/**
	 * @param $currentTick
	 *
	 * @return bool
	 */
	public function onUpdate($currentTick) {
		if($this->closed) {
			return false;
		}
		$petOwner = $this->getPetOwner();
		if($petOwner === null) {
			$this->ridden = false;
			$this->rider = null;
			$this->despawnFromAll();
			return false;
		}
		if($this->getLevel()->getId() !== $petOwner->getLevel()->getId()) {
			$this->getLoader()->createPet($this->getEntityType(), $this->getPetOwner(), $this->getPetName(), $this->getStartingScale(), $this->namedtag["isBaby"], $this->getPetLevel());
			$this->close();
			return false;
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
	 * @return string
	 */
	public function getEntityType(): string {
		return str_replace(" ", "", str_replace("Pet", "", $this->getName()));
	}

	/**
	 * @param $motionX
	 * @param $motionZ
	 *
	 * @return mixed
	 */
	public abstract function doRidingMovement($motionX, $motionZ);
}
