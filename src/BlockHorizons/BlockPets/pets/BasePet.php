<?php

namespace BlockHorizons\BlockPets\pets;

use pocketmine\entity\Creature;
use pocketmine\level\format\Chunk;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

abstract class BasePet extends Creature {

	public $name;
	public $speed = 1.0;
	public $scale = 1.0;
	public $networkId;
	protected $petOwner;
	protected $petLevel = 1;

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
		return $this->petLevel;
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

		$this->petLevel = $this->namedtag["petLevel"];
		$this->petOwner = $this->namedtag["petOwner"];
		$this->speed = $this->namedtag["speed"];
		$this->scale = $this->namedtag["scale"];
		$this->networkId = $this->namedtag["networkId"];
	}

	public function initEntity() {
		parent::initEntity();
		$this->setDataProperty(self::DATA_FLAG_NO_AI, self::DATA_TYPE_BYTE, true);
		$this->setDataProperty(self::DATA_SCALE, self::DATA_TYPE_FLOAT, $this->getScale());
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

	public function saveToArray() {
		$entityInfo = [];
		$entityInfo["nbt"] = $this->namedtag;
	}
}
