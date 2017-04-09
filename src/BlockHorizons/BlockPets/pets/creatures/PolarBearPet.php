<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class PolarBearPet extends WalkingPet {

	public $height = 1.6;
	public $width = 1.2;
	public $speed = 1.1;

	public $name = "Polar Bear Pet";
	public $tier = self::TIER_UNCOMMON;

	public $networkId = 29;
}