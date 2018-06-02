<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class LlamaPet extends WalkingPet {

	const NETWORK_ID = self::LLAMA;

	public $height = 1.87;
	public $width = 0.9;

	public $name = "Llama Pet";

	public function generateCustomPetData(): void {
		$randomVariant = random_int(0, 3);
		$this->getDataPropertyManager()->setInt(self::DATA_VARIANT, $randomVariant);
	}
}