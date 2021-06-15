<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class SnowGolemPet extends WalkingPet {

	const NETWORK_NAME = "SNOW_GOLEM_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:snow_golem";

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
