<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class SheepPet extends WalkingPet {

	public $height = 1.2;
	public $width = 0.8;

	public $name = "Sheep Pet";

	public $networkId = 13;

	public function generateCustomPetData() {
		$randomColour = mt_rand(0, 15);
		$this->setDataProperty(self::DATA_COLOUR, self::DATA_TYPE_INT, $randomColour);
	}
}