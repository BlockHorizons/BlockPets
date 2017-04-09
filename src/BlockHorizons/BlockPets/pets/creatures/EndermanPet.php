<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class EndermanPet extends WalkingPet {

	public $height = 2.8;
	public $width = 0.72;
	public $speed = 1.2;

	public $name = "Enderman Pet";
	public $tier = self::TIER_SPECIAL;

	public $networkId = 38;
}