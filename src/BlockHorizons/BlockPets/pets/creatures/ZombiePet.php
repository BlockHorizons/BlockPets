<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class ZombiePet extends WalkingPet {

	public $networkId = 32;

	public $name = "Zombie Pet";
	public $tier = self::TIER_COMMON;

	public $width = 0.72;
	public $height = 1.8;
}