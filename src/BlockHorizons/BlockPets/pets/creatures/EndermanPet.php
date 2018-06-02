<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class EndermanPet extends WalkingPet {

	const NETWORK_ID = self::ENDERMAN;

	public $height = 2.8;
	public $width = 0.72;

	public $name = "Enderman Pet";
}