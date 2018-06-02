<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class CowPet extends WalkingPet {

	const NETWORK_ID = self::COW;

	public $height = 1.3;
	public $width = 0.9;

	public $name = "Cow Pet";
}