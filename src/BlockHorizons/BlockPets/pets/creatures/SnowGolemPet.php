<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class SnowGolemPet extends WalkingPet {

	const NETWORK_ID = self::SNOW_GOLEM;

	public $height = 1.9;
	public $width = 0.7;

	public $name = "Snow Golem Pet";

	public function generateCustomPetData(): void {
		if($this->getPetName() !== "shoghicp") {
			return;
		}
		$this->setGenericFlag(self::DATA_FLAG_SHEARED, true);
	}
}
