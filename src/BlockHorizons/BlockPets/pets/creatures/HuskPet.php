<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class HuskPet extends WalkingPet {

	const NETWORK_ID = self::HUSK;

	public $height = 1.95;
	public $width = 0.6;

	public $name = "Husk Pet";
}
