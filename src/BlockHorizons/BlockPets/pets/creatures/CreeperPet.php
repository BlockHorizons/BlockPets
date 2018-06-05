<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class CreeperPet extends WalkingPet {

	const NETWORK_ID = self::CREEPER;

	public $height = 1.7;
	public $width = 0.6;

	public $name = "Creeper Pet";
}
