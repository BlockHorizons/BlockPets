<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class VillagerPet extends WalkingPet {

	public const PROFESSION_FARMER = 0;
	public const PROFESSION_LIBRARIAN = 1;
	public const PROFESSION_PRIEST = 2;
	public const PROFESSION_BLACKSMITH = 3;
	public const PROFESSION_BUTCHER = 4;

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "villager";
	protected const PET_NETWORK_ID = self::VILLAGER;

	public $height = 1.95;
	public $width = 0.6;

	public $name = "Villager Pet";

	public function saveNBT(): void {
		parent::saveNBT();
		$this->namedtag->setInt("Profession", $this->getProfession());
	}

	protected function initEntity(): void {
		parent::initEntity();

		/** @var int $profession */
		$profession = $this->namedtag->getInt("Profession", self::PROFESSION_FARMER);

		if($profession > 4 or $profession < 0){
			$profession = self::PROFESSION_FARMER;
		}

		$this->setProfession($profession);
	}

	/**
	 * Sets the villager profession
	 *
	 * @param int $profession
	 */
	public function setProfession(int $profession) : void{
		$this->propertyManager->setInt(self::DATA_VARIANT, $profession);
	}

	public function getProfession() : int{
		return $this->propertyManager->getInt(self::DATA_VARIANT);
	}
}
