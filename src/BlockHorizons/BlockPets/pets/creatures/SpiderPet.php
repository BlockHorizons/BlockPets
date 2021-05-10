<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

class SpiderPet extends WalkingPet implements SmallCreature {

	const NETWORK_NAME = "SPIDER_PET";
	const NETWORK_ORIG_ID = self::SPIDER;

	public $height = 0.9;
	public $width = 1.4;

	public $name = "Spider Pet";

	public function generateCustomPetData(): void {
		$this->setGenericFlag(self::DATA_FLAG_CAN_CLIMB, true);
	}
}
