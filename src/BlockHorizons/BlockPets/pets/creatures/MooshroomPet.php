<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class MooshroomPet extends WalkingPet {

	public $height = 1.3;
	public $width = 0.9;
	public $speed = 1.2;

	public $name = "Mooshroom Pet";
	public $tier = self::TIER_SPECIAL;

	public $networkId = 16;
}