<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;

class BlazePet extends HoveringPet {

	const NETWORK_NAME = "BLAZE_PET";
	const NETWORK_ORIG = self::BLAZE;

	public $width = 0.6;
	public $height = 1.8;

	public $name = "Blaze Pet";
}
