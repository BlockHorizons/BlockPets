<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SwimmingPet;

class SquidPet extends SwimmingPet {

	const NETWORK_ID = self::SQUID;

	public $width = 0.8;
	public $height = 0.8;

	public $name = "Squid Pet";
}