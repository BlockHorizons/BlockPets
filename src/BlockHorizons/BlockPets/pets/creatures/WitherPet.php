<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;

class WitherPet extends HoveringPet {

	const NETWORK_NAME = "WITHER_PET";
	const NETWORK_ORIG_ID = self::WITHER;

	public $height = 3.5;
	public $width = 0.9;

	public $name = "Wither Pet";
}
