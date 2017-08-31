<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class LlamaPet extends WalkingPet {

	public $height = 1.87;
	public $width = 0.9;

	public $name = "Llama Pet";
	public $networkId = 29;

	public function generateCustomPetData(): void {
		$randomVariant = random_int(0, 3);
		$this->setDataProperty(self::DATA_VARIANT, self::DATA_TYPE_INT, $randomVariant);
	}
}