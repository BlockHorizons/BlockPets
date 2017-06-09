<?php

namespace BlockHorizons\BlockPets\pets;

use pocketmine\utils\TextFormat;

class Calculator {

	private $pet;

	public function __construct(BasePet $pet) {
		$this->pet = $pet;
	}

	public function recalculateAll() {
		$this->recalculateHealth();
		$this->recalculateSize();
		$this->recalculateDamage();
		$this->updateNameTag();
		$this->storeToDatabase();
	}

	public function recalculateHealth() {
		$petLevel = $this->getPet()->getPetLevel();
		$baseHealth = $this->getPet()->getLoader()->getBlockPetsConfig()->getBasePetHealth();
		$scalingHealth = $this->getPet()->getLoader()->getBlockPetsConfig()->getPetHealthPerLevel();

		$this->getPet()->setMaxHealth((int) round($baseHealth + $scalingHealth * $petLevel));
		$this->getPet()->fullHeal();
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

	public function updateNameTag() {
		$percentage = (int) ($this->getPet()->getPetLevelPoints() / $this->getPet()->getRequiredLevelPoints($this->getPet()->getPetLevel()) * 100);
		$this->getPet()->setNameTag(
			$this->getPet()->getPetName() . PHP_EOL .
			TextFormat::GRAY . "Lvl." . TextFormat::AQUA . $this->getPet()->getPetLevel() . TextFormat::GRAY . " (" . TextFormat::YELLOW . $percentage . TextFormat::GRAY . "%) " . TextFormat::GRAY . $this->getPet()->getName()
		);
	}

	public function storeToDatabase() {
		if($this->getPet()->getLoader()->getBlockPetsConfig()->storeToDatabase()) {
			if($this->getPet()->getLoader()->getDatabase()->petExists($this->getPet()->getName(), $this->getPet()->getPetOwnerName())) {
				$this->getPet()->getLoader()->getDatabase()->updatePetExperience($this->getPet()->getName(), $this->getPet()->getPetOwnerName(), $this->getPet()->getPetLevel(), $this->getPet()->getPetLevelPoints());
			} else {
				$this->getPet()->getLoader()->getDatabase()->registerPet($this->getPet());
			}
		}
	}
}
