<?php

namespace BlockHorizons\BlockPets\pets;

use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;

abstract class IrasciblePet extends BasePet {

	private $target = null;
	private $isAttacking = false;

	public function __construct(Level $level, CompoundTag $nbt) {
		parent::__construct($level, $nbt);
	}

	public function setAngry(Entity $player) {
		$this->target = $player;
	}

	public function calmDown() {
		$this->target = null;
	}

	/**
	 * @return bool
	 */
	public function isAttacking(): bool {
		return $this->isAttacking;
	}

	/**
	 * @return Player
	 */
	public function getTarget(): Player {
		return $this->target;
	}

	/**
	 * @return bool
	 */
	public function isAngry(): bool {
		return $this->target !== null;
	}

	public abstract function doAttackingMovement();
}