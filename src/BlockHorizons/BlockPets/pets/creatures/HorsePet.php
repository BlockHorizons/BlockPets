<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class HorsePet extends WalkingPet {

	public $speed = 1.8;
	public $networkId = 23;

	public $name = "Horse Pet";
	public $tier = self::TIER_LEGENDARY;

	public $width = 1.4;
	public $height = 1.6;
}