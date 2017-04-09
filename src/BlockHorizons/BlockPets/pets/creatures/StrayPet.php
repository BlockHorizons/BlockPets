<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class StrayPet extends WalkingPet {

	public $height = 1.9;
	public $width = 0.6;
	public $speed = 1.1;

	public $name = "Stray Pet";
	public $tier = self::TIER_UNCOMMON;

	public $networkId = 46;
}