<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class GoatPet extends WalkingPet {

	const NETWORK_NAME = "GOAT_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:goat";

	public $height = 0.9;
	public $width = 1.3;

	public $name = "Goat Pet";

	public function generateCustomPetData(): void {
		$randomVariant = random_int(0, 1);
		$this->getDataPropertyManager()->setInt(self::DATA_VARIANT, $randomVariant);
	}
}
