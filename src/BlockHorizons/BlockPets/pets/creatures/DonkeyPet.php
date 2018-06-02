<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class DonkeyPet extends WalkingPet {

	const NETWORK_ID = self::DONKEY;

	public $name = "Donkey Pet";

	public $width = 1.4;
	public $height = 1.6;
}