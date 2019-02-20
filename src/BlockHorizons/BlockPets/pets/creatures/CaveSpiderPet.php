<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

class CaveSpiderPet extends WalkingPet implements SmallCreature {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "cave_spider";
	protected const PET_NETWORK_ID = self::CAVE_SPIDER;

	public $speed = 1.4;
	public $height = 0.5;
	public $width = 0.7;

	public $name = "Cave Spider Pet";

	public function generateCustomPetData(): void {
		$this->setGenericFlag(self::DATA_FLAG_CAN_CLIMB, true);
	}
}
