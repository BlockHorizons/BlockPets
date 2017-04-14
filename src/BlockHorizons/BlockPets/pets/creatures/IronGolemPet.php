<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class IronGolemPet extends WalkingPet {

	public $height = 2.7;
	public $width = 1.9;
	public $speed = 1.4;

	public $name = "Iron Golem Pet";
	public $tier = self::TIER_SPECIAL;

	public $networkId = 20;
}