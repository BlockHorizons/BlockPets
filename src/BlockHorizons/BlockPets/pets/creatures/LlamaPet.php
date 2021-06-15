<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class LlamaPet extends WalkingPet {

	const NETWORK_NAME = "LLAMA_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:llama";

	public $height = 0.935;
	public $width = 0.45;

	public $name = "Llama Pet";

	public function generateCustomPetData(): void {
		$randomVariant = random_int(0, 3);
		$this->getDataPropertyManager()->setInt(self::DATA_VARIANT, $randomVariant);
	}
}
