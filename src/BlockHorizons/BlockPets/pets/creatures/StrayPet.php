<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class StrayPet extends WalkingPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "stray";
	protected const PET_NETWORK_ID = self::STRAY;

	public $height = 1.99;
	public $width = 0.6;

	public $name = "Stray Pet";
}
