<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class MooshroomPet extends WalkingPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "mooshroom";
	protected const PET_NETWORK_ID = self::MOOSHROOM;

	public $height = 1.4;
	public $width = 0.9;

	public $name = "Mooshroom Pet";
}
