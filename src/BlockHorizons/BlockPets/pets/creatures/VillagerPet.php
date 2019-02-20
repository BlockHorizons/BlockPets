<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class VillagerPet extends WalkingPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "villager";
	protected const PET_NETWORK_ID = self::VILLAGER;

	public $height = 1.95;
	public $width = 0.6;

	public $name = "Villager Pet";

	public function generateCustomPetData(): void {
		$randomVariant = random_int(0, 5);
		$this->getDataPropertyManager()->setInt(self::DATA_VARIANT, $randomVariant);
	}
}
