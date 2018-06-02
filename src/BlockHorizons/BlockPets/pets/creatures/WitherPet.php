<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;

class WitherPet extends HoveringPet {

	const NETWORK_ID = self::WITHER;

	public $height = 3;
	public $width = 3;

	public $name = "Wither Pet";
}