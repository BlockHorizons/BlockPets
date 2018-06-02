<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;

class EnderDragonPet extends HoveringPet {

	const NETWORK_ID = self::ENDER_DRAGON;

	public $name = "Ender Dragon Pet";

	public $width = 2.5;
	public $height = 1;
}
