<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class PigPet extends WalkingPet {

	public $height = 0.9;
	public $width = 0.7;

	public $name = "Pig Pet";
	public $tier = self::TIER_COMMON;

	public $networkId = 12;
}