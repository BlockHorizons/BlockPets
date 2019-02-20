<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class WitchPet extends WalkingPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "witch";
	protected const PET_NETWORK_ID = self::WITCH;

	public $height = 1.95;
	public $width = 0.6;

	public $name = "Witch Pet";
}
