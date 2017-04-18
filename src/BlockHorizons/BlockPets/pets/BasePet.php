<?php

namespace BlockHorizons\BlockPets\pets;

use BlockHorizons\BlockPets\pets\creatures\EnderDragonPet;
use pocketmine\entity\Creature;
use pocketmine\entity\Rideable;
use pocketmine\level\format\Chunk;
use pocketmine\level\particle\LavaParticle;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\network\protocol\SetEntityLinkPacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

abstract class BasePet extends Creature implements Rideable {

	public $name;
	public $speed = 1.0;
	public $scale = 1.0;
	public $networkId;
	protected $tier = 1;
	protected $petOwner;
	protected $petLevel = 1;
	private $currentPetLevel = 1;

	protected $ridden = false;
	protected $rider = null;

	const STATE_SITTING = 2;
	const STATE_STANDING = 3;

	const TIER_COMMON = 1;
	const TIER_UNCOMMON = 2;
	const TIER_SPECIAL = 3;
	const TIER_EPIC = 4;
	const TIER_LEGENDARY = 5;

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
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
	public function getNetworkId(): int {
		return $this->networkId;
	}

	/**
	 * @return Player|null
	 */
	public function getPetOwner() {
		return $this->getLevel()->getServer()->getPlayer($this->petOwner);
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
	public function getScale(): float {
		return $this->scale;
	}

	/**
	 * @return int
	 */
	public function getPetLevel(): int {
		return $this->petLevel ?? 1;
	}

	/**
	 * @return int
	 */
	public function getCurrentPetLevel(): int {
		return $this->currentPetLevel ?? 0;
	}

	public function levelUp() {
		$this->petLevel += 1;
		$this->level->addParticle(new LavaParticle($this));
	}

	public function levelUpCurrentLevel() {
		$this->currentPetLevel += 1;
	}

	/**
	 * @param float $value
	 */
	public function setScale(float $value) {
		$multiplier = $value / $this->getScale();
		$this->width *= $multiplier;
		$this->height *= $multiplier;
		$halfWidth = $this->width / 2;
		$this->boundingBox->setBounds(
			$this->x - $halfWidth,
			$this->y,
			$this->z - $halfWidth,
			$this->x + $halfWidth,
			$this->y + $this->height,
			$this->z + $halfWidth
		);
		$this->setDataProperty(self::DATA_SCALE, self::DATA_TYPE_FLOAT, $value);
		$this->setDataProperty(self::DATA_BOUNDING_BOX_WIDTH, self::DATA_TYPE_FLOAT, $this->width);
		$this->setDataProperty(self::DATA_BOUNDING_BOX_HEIGHT, self::DATA_TYPE_FLOAT, $this->height);
	}

	/**
	 * @return string
	 */
	public function getPetName(): string {
		return $this->getName() . TextFormat::GRAY . " - Level " . $this->getPetLevel();
	}

	public function __construct(Chunk $chunk, CompoundTag $nbt) {
		parent::__construct($chunk, $nbt);
		$this->setNameTagVisible(true);
		$this->setNameTagAlwaysVisible(true);

		$this->petOwner = $this->namedtag["petOwner"];
		$this->petLevel = $this->namedtag["petLevel"];
		$this->scale = $this->namedtag["scale"];
		$this->currentPetLevel = $this->namedtag["currentPetLevel"];

		//$this->setNameTag(TextFormat::GRAY . "Lvl" . $this->getPetLevel() . " " . $this->getName() . " - " . $this->getNameTag());
		$this->setScale($this->scale);
	}

	public function initEntity() {
		parent::initEntity();
		$this->setDataProperty(self::DATA_FLAG_NO_AI, self::DATA_TYPE_BYTE, true);
	}

	public function spawnTo(Player $player) {
		parent::spawnTo($player);
		$pk = new AddEntityPacket();
		$pk->eid = $this->getId();
		$pk->type = $this->getNetworkId();
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->speedX = 0;
		$pk->speedY = 0;
		$pk->speedZ = 0;
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk);
	}

	public function saveNBT() {
		parent::saveNBT();
		$this->namedtag->petOwner = new StringTag("petOwner", $this->getPetOwnerName());
		$this->namedtag->petLevel = new IntTag("petLevel", $this->getPetLevel());
		$this->namedtag->currentPetLevel = new IntTag("currentPetLevel", $this->getCurrentPetLevel());
		$this->namedtag->speed = new FloatTag("speed", $this->getSpeed());
		$this->namedtag->scale = new FloatTag("scale", $this->getScale());
		$this->namedtag->networkId = new IntTag("networkId", $this->getNetworkId());
	}

	/**
	 * @return array
	 */
	public function getDrops(): array {
		return [];
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
		$this->getPetOwner()->setDataProperty(57, self::DATA_TYPE_VECTOR3F, [0, 1.8 + $this->getScale() * 0.8, -0.25]);
		if($this instanceof EnderDragonPet) {
			$this->getPetOwner()->setDataProperty(57, self::DATA_TYPE_VECTOR3F, [0, 2.5 + $this->getScale(), -1.7]);
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
	}
	
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
		$this->getPetOwner()->canCollide = true;
		$this->setDataFlag(self::DATA_FLAG_SADDLED, self::DATA_TYPE_BYTE, false);
	}

	/**
	 * @return bool
	 */
	public function isRidden(): bool {
		return $this->ridden;
	}

	public function onUpdate($currentTick) {
		$petOwner = $this->getPetOwner();
		if($petOwner === null) {
			$this->ridden = false;
			$this->rider = null;
			$this->despawnFromAll();
			return false;
		}
		if($this->distance($petOwner) >= 50 || $this->getLevel()->getName() !== $petOwner->getLevel()->getName()) {
			$this->teleport($petOwner->getPosition());
			$this->despawnFromAll();
			$this->spawnToAll();
		}
		/*if($this->getPetLevel() !== $this->getCurrentPetLevel()) {
			$this->levelUpCurrentLevel();
			$this->setNameTag(TextFormat::GRAY . "Lvl" . $this->getPetLevel() . " " . $this->getName() . " - " . explode(" - ", $this->getNameTag())[1]);
		}*/

		if($this->isRidden()) {
			$this->doRidingMovement($currentTick);
		}
		parent::onUpdate($currentTick);
		return true;
	}

	public abstract function doRidingMovement($currentTick);
}
