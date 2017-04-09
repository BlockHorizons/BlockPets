<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class HuskPet extends WalkingPet {

	public $height = 1.75;
	public $width = 0.8;
	public $speed = 1.1;

	public $name = "Husk Pet";
	public $tier = self::TIER_UNCOMMON;

	public $networkId = 47;
}