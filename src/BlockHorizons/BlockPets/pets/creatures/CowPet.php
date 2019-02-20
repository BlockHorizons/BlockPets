<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class CowPet extends WalkingPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "cow";
	protected const PET_NETWORK_ID = self::COW;

	public $height = 1.4;
	public $width = 0.9;

	public $name = "Cow Pet";
}
