<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class HuskPet extends WalkingPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "husk";
	protected const PET_NETWORK_ID = self::HUSK;

	public $height = 1.95;
	public $width = 0.6;

	public $name = "Husk Pet";
}
