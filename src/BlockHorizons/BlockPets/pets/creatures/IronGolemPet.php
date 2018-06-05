<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class IronGolemPet extends WalkingPet {

	const NETWORK_ID = self::IRON_GOLEM;

	public $height = 2.7;
	public $width = 1.4;

	public $name = "Iron Golem Pet";
}
