<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class VillagerPet extends WalkingPet {

	const NETWORK_NAME = "VILLAGER_PET";
	const NETWORK_ORIG = self::VILLAGER;

	public $height = 1.95;
	public $width = 0.6;

	public $name = "Villager Pet";

	public function generateCustomPetData(): void {
		$randomVariant = random_int(0, 5);
		$this->getDataPropertyManager()->setInt(self::DATA_VARIANT, $randomVariant);
	}
}
