<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class SpiderPet extends WalkingPet {

	public $height = 1.12;
	public $width = 1.3;
	public $speed = 1.2;

	public $name = "Spider Pet";
	public $tier = self::TIER_SPECIAL;

	public $networkId = 35;
}