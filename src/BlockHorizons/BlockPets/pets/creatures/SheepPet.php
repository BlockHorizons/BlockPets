<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class SheepPet extends WalkingPet {

	const NETWORK_ID = self::SHEEP;

	public $height = 1.2;
	public $width = 0.8;

	public $name = "Sheep Pet";

	public function generateCustomPetData(): void {
		$randomColour = random_int(0, 15);
		$this->getDataPropertyManager()->setByte(self::DATA_COLOUR, $randomColour);
	}
}