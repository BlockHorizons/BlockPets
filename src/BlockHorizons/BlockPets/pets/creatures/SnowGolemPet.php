<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class SnowGolemPet extends WalkingPet {

	public $height = 1.7;
	public $width = 0.9;

	public $name = "Snow Golem Pet";
	public $networkId = 21;

	public function generateCustomPetData() {
		if(!$this->getPetName() === "shoghicp") {
			return;
		}
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_SHEARED, true);
	}
}