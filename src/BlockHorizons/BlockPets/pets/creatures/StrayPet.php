<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class StrayPet extends WalkingPet {

	const NETWORK_ID = self::STRAY;

	public $height = 1.9;
	public $width = 0.6;

	public $name = "Stray Pet";
}