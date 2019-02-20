<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class PolarBearPet extends WalkingPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "polar_bear";
	protected const PET_NETWORK_ID = self::POLAR_BEAR;

	public $height = 1.4;
	public $width = 1.3;

	public $name = "Polar Bear Pet";
}
