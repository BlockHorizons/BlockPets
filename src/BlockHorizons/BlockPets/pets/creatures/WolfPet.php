<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class WolfPet extends WalkingPet {

	public $speed = 1.6;
	public $networkId = 14;

	public $name = "Wolf Pet";
	public $tier = self::TIER_EPIC;

	public $width = 0.72;
	public $height = 0.9;
}