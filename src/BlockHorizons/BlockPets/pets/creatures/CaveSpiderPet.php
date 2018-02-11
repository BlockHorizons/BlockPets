<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

class CaveSpiderPet extends WalkingPet implements SmallCreature {

	public $speed = 1.4;
	public $height = 0.8;
	public $width = 0.9;

	public $name = "Cave Spider Pet";

	public $networkId = 40;

	public function generateCustomPetData(): void {
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_CAN_CLIMB, true);
	}
}