<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets;

use pocketmine\utils\TextFormat;

class Calculator {

	/** @var BasePet */
	private $pet;

	public function __construct(BasePet $pet) {
		$this->pet = $pet;
	}

	/**
	 * Recalculates every property of the pet and saves/updates it to the database.
	 */
	public function recalculateAll(): void {
		$this->recalculateHealth();
		$this->recalculateSize();
		$this->recalculateDamage();
		$this->updateNameTag();
		$this->storeToDatabase();
	}

	/**
	 * Recalculates maximum health that the pet should have according to its configuration scalings.
	 */
	public function recalculateHealth(): void {
		$petLevel = $this->getPet()->getPetLevel();
		$baseHealth = $this->getPet()->getLoader()->getBlockPetsConfig()->getBasePetHealth();
		$scalingHealth = $this->getPet()->getLoader()->getBlockPetsConfig()->getPetHealthPerLevel();

		$this->getPet()->setMaxHealth((int) floor($baseHealth + $scalingHealth * $petLevel));
		$this->getPet()->fullHeal();
	}

	/**
	 * @return BasePet
	 */
	public function getPet(): BasePet {
		return $this->pet;
	}

	/**
	 * Recalculates size that the pet should have according to its configuration scalings.
	 *
	 * @return bool
	 */
	public function recalculateSize(): bool {
		if($this->getPet()->getPetOwner() === null) {
			return false;
		}
		$petLevel = $this->getPet()->getPetLevel();
		$scalingSize = $this->getPet()->getLoader()->getBlockPetsConfig()->getPetSizePerLevel();

		$this->getPet()->setScale((float) ($this->getPet()->getStartingScale() + $scalingSize * $petLevel));
		if($this->getPet()->getScale() > $this->getPet()->getMaxSize() && !($this->getPet()->getPetOwner()->hasPermission("blockpets.bypass-size-limit"))) {
			$this->getPet()->setScale($this->getPet()->getMaxSize());
		}
		return true;
	}

	/**
	 * Recalculates attack damage that the pet should have according to its configuration attack damage.
	 */
	public function recalculateDamage(): void {
		$petLevel = $this->getPet()->getPetLevel();
		$baseDamage = $this->getPet()->getLoader()->getBlockPetsConfig()->getBasePetDamage();
		$scalingDamage = $this->getPet()->getLoader()->getBlockPetsConfig()->getPetDamagePerLevel();

		$this->getPet()->setAttackDamage((int) round($baseDamage + $scalingDamage * $petLevel));
	}

	/**
	 * Updates the name tag to include the latest data like level, level points etc.
	 */
	public function updateNameTag(): void {
		$percentage = round($this->getPet()->getPetLevelPoints() / $this->getPet()->getRequiredLevelPoints($this->getPet()->getPetLevel()) * 100, 1);
		$this->getPet()->setNameTag(
			$this->getPet()->getPetName() . "\n" .
			TextFormat::GRAY . "Lvl." . TextFormat::AQUA . $this->getPet()->getPetLevel() . TextFormat::GRAY . " (" . TextFormat::YELLOW . $percentage . TextFormat::GRAY . "%) " . TextFormat::GRAY . $this->getPet()->getName() .
			TextFormat::RED . " (" . $this->getPet()->getHealth() . "/" . $this->getPet()->getMaxHealth() . ")"
		);
	}

	/**
	 * Stores the pet to the database, or updates level and level points if the pet has already been added to the database.
	 */
	public function storeToDatabase(): void {
		$pet = $this->getPet();
		$database = $pet->getLoader()->getDatabase();
		$database->updateExperience($pet);
		$database->updateChested($pet);
	}
}
