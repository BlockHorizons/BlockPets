<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class WitchPet extends WalkingPet {

	const NETWORK_NAME = "WITCH_PET";
	const NETWORK_ORIG = self::WITCH;

	public $height = 1.95;
	public $width = 0.6;

	public $name = "Witch Pet";
}
