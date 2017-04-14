<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class SnowGolemPet extends WalkingPet {

	public $height = 1.7;
	public $width = 0.9;
	public $speed = 1.2;

	public $name = "Snow Golem Pet";
	public $tier = self::TIER_UNCOMMON;

	public $networkId = 21;
}