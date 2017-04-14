<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class ChickenPet extends WalkingPet {

	public $tier = self::TIER_UNCOMMON;
	public $width = 0.4;
	public $height = 0.7;
	public $speed = 1.2;

	public $name = "Chicken Pet";

	public $networkId = 10;
}