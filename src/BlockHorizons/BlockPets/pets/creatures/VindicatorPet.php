<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class VindicatorPet extends WalkingPet {

	const NETWORK_NAME = "VINDICATOR_PET";
	const NETWORK_ORIG = self::VINDICATOR;

	public $name = "Vindicator Pet";

	public $width = 0.6;
	public $height = 1.95;
}
