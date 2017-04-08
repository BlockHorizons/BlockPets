<?php

namespace BlockHorizons\BlockPets\pets;

use pocketmine\entity\Creature;
use pocketmine\entity\Rideable;
use pocketmine\level\format\Chunk;
use pocketmine\math\Vector3;
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
	protected $petOwner;
	protected $petLevel = 1;

	protected $ridden = false;
	protected $rider = null;

	const STATE_SITTING = 2;
	const STATE_STANDING = 3;

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
		if($this->namedtag["petOwner"] === null) {
			return null;
		}
		return $this->getLevel()->getServer()->getPlayer($this->namedtag["petOwner"]);
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
		return $this->petLevel;
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
		$this->scale = $this->namedtag["scale"];
		$this->setDataProperty(59, self::DATA_TYPE_FLOAT, 2.5);

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
	 * @param Player $player
	 */
	public function setRider(Player $player) {
		$this->ridden = true;
		$this->rider = $player->getName();

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
		$this->server->broadcastPacket($this->level->getPlayers(), $pk);

		$pk = new SetEntityLinkPacket();
		$pk->from = $this->getPetOwner()->getId();
		$pk->to = 0;
		$pk->type = self::STATE_STANDING;
		$this->getPetOwner()->dataPacket($pk);
	}

	/**
	 * @return bool
	 */
	public function isRidden(): bool {
		return $this->ridden;
	}
}
