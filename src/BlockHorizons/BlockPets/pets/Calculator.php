<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets;

use pocketmine\utils\TextFormat;

class Calculator {

	/** @var BasePet */
	private $pet;

	public function __construct($pet) {
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
	}

	/**
	 * Recalculates maximum health that the pet should have according to its configuration scalings.
	 */
	public function recalculateHealth(): void {
		$pet = $this->getPet();
		$bpConfig = $pet->getLoader()->getBlockPetsConfig();

		$petLevel = $pet->getPetLevel();
		$baseHealth = $bpConfig->getBasePetHealth();
		$scalingHealth = $bpConfig->getPetHealthPerLevel();

		$pet->setMaxHealth((int) floor($baseHealth + $scalingHealth * $petLevel));
		$pet->fullHeal();
	}

	/**
	 * @return BasePet
	 */
	public function getPet() {
		return $this->pet;
	}

	/**
	 * Recalculates size that the pet should have according to its configuration scalings.
	 *
	 * @return bool
	 */
	public function recalculateSize(): bool {
		$pet = $this->getPet();
		$petOwner = $pet->getPetOwner();
		if($petOwner === null) {
			return false;
		}

		if($pet->getScale() > $pet->getMaxSize() && !($petOwner->hasPermission("blockpets.bypass-size-limit"))) {
			$pet->setScale($pet->getMaxSize());
		} else {
			$petLevel = $pet->getPetLevel();
			$scalingSize = $pet->getLoader()->getBlockPetsConfig()->getPetSizePerLevel();
			$pet->setScale((float) ($pet->getStartingScale() + $scalingSize * $petLevel));
		}
		return true;
	}

	/**
	 * Recalculates attack damage that the pet should have according to its configuration attack damage.
	 */
	public function recalculateDamage(): void {
		$pet = $this->getPet();
		$bpConfig = $pet->getLoader()->getBlockPetsConfig();

		$petLevel = $pet->getPetLevel();
		$baseDamage = $bpConfig->getBasePetDamage();
		$scalingDamage = $bpConfig->getPetDamagePerLevel();

		$pet->setAttackDamage((int) round($baseDamage + $scalingDamage * $petLevel));
	}

	/**
	 * Updates the name tag to include the latest data like level, level points etc.
	 */
	public function updateNameTag(): void {
		$pet = $this->getPet();
		$percentage = round($pet->getPetLevelPoints() / LevelCalculator::getRequiredLevelPoints($pet->getPetLevel()) * 100, 1);
		$pet->setNameTag(
			$pet->getPetName() . "\n" .
			TextFormat::GRAY . "Lvl." . TextFormat::AQUA . $pet->getPetLevel() . TextFormat::GRAY . " (" . TextFormat::YELLOW . $percentage . TextFormat::GRAY . "%) " . TextFormat::GRAY . $pet->getName() .
			TextFormat::RED . " (" . $pet->getHealth() . "/" . $pet->getMaxHealth() . ")"
		);
	}
}
