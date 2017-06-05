<?php

namespace BlockHorizons\BlockPets\pets;

use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;

abstract class Calculator extends BasePet {

	public function __construct(Level $level, CompoundTag $nbt) {
		parent::__construct($level, $nbt);
	}

	public function recalculateAll() {
		$this->recalculateHealth();
		$this->recalculateSize();
		$this->recalculateDamage();
	}

	public function recalculateHealth() {
		$petLevel = $this->getPetLevel();
		$baseHealth = $this->getLoader()->getBlockPetsConfig()->getBasePetHealth();
		$scalingHealth = $this->getLoader()->getBlockPetsConfig()->getPetHealthPerLevel();

		$this->setMaxHealth((int) round($baseHealth + $scalingHealth * $petLevel));
	}

	public function recalculateSize() {
		$petLevel = $this->getPetLevel();
		$scalingSize = $this->getLoader()->getBlockPetsConfig()->getPetSizePerLevel();

		$this->setScale((float) ($this->getStartingScale() + $scalingSize * $petLevel));
	}

	public function recalculateDamage() {
		$petLevel = $this->getPetLevel();
		$baseDamage = $this->getLoader()->getBlockPetsConfig()->getBasePetDamage();
		$scalingDamage = $this->getLoader()->getBlockPetsConfig()->getPetDamagePerLevel();

		$this->setAttackDamage((int) round($baseDamage + $scalingDamage * $petLevel));
	}
}
