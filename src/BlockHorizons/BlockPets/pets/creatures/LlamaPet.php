<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class LlamaPet extends WalkingPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "llama";
	protected const PET_NETWORK_ID = self::LLAMA;

	public $height = 0.935;
	public $width = 0.45;

	public $name = "Llama Pet";

	public function generateCustomPetData(): void {
		$randomVariant = random_int(0, 3);
		$this->getDataPropertyManager()->setInt(self::DATA_VARIANT, $randomVariant);
	}
}
