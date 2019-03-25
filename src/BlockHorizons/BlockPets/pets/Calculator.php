<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets;

use BlockHorizons\BlockPets\pets\utils\LevelCalculator;

use pocketmine\utils\TextFormat;

class Calculator {

	/** @var BasePet */
	private $pet;

	/** @var bool */
	private $needs_update = false;

	public function __construct(BasePet $pet) {
		$this->pet = $pet;
	}

	public function flagForUpdate(bool $value = true): void {
		$this->needs_update = $value;
	}

	public function isFlaggedForUpdate(): bool {
		return $this->needs_update;
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
	public function getPet(): BasePet {
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

		$pet_level = $pet->getPetLevel();

		$percentage = ($pet->getPetLevelPoints() / LevelCalculator::getLevelPoints($pet_level)) * 100;
		$pet->setNameTag(
			$pet->getPetName() . "\n" .
			TextFormat::GRAY . "Lvl." . TextFormat::AQUA . $pet_level . TextFormat::GRAY . " (" . TextFormat::YELLOW . round($percentage, 1) . TextFormat::GRAY . "%) " . TextFormat::GRAY . $pet->getName() .
			TextFormat::RED . " (" . $pet->getHealth() . "/" . $pet->getMaxHealth() . ")"
		);
	}
}
