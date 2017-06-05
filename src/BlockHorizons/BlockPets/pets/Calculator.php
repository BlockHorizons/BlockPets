<?php

namespace BlockHorizons\BlockPets\pets;


class Calculator {

	private $pet;

	public function __construct(BasePet $pet) {
		$this->pet = $pet;
	}

	public function recalculateAll() {
		$this->recalculateHealth();
		$this->recalculateSize();
		$this->recalculateDamage();
	}

	public function recalculateHealth() {
		$petLevel = $this->getPet()->getPetLevel();
		$baseHealth = $this->getPet()->getLoader()->getBlockPetsConfig()->getBasePetHealth();
		$scalingHealth = $this->getPet()->getLoader()->getBlockPetsConfig()->getPetHealthPerLevel();

		$this->getPet()->setMaxHealth((int) round($baseHealth + $scalingHealth * $petLevel));
	}

	/**
	 * @return BasePet
	 */
	public function getPet(): BasePet {
		return $this->pet;
	}

	public function recalculateSize() {
		$petLevel = $this->getPet()->getPetLevel();
		$scalingSize = $this->getPet()->getLoader()->getBlockPetsConfig()->getPetSizePerLevel();

		$this->getPet()->setScale((float) ($this->getPet()->getStartingScale() + $scalingSize * $petLevel));
	}

	public function recalculateDamage() {
		$petLevel = $this->getPet()->getPetLevel();
		$baseDamage = $this->getPet()->getLoader()->getBlockPetsConfig()->getBasePetDamage();
		$scalingDamage = $this->getPet()->getLoader()->getBlockPetsConfig()->getPetDamagePerLevel();

		$this->getPet()->setAttackDamage((int) round($baseDamage + $scalingDamage * $petLevel));
	}
}