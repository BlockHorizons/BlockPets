<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class EndermanPet extends WalkingPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "enderman";
	protected const PET_NETWORK_ID = self::ENDERMAN;

	public $height = 2.9;
	public $width = 0.6;

	public $name = "Enderman Pet";
}
