<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

class SpiderPet extends WalkingPet implements SmallCreature {

	const NETWORK_ID = self::SPIDER;

	public $height = 1.12;
	public $width = 1.3;

	public $name = "Spider Pet";

	public function generateCustomPetData(): void {
		$this->propertyManager->setPropertyValue(self::DATA_FLAGS, self::DATA_FLAG_CAN_CLIMB, true);
	}
}