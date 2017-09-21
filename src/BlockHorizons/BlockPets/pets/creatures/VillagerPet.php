<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class VillagerPet extends WalkingPet {

	public $height = 1.8;
	public $width = 0.8;

	public $name = "Villager Pet";
	public $networkId = 15;

	public function generateCustomPetData(): void {
		$randomVariant = random_int(0, 5);
		$this->setDataProperty(self::DATA_VARIANT, self::DATA_TYPE_INT, $randomVariant);
	}
}