<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class WitchPet extends WalkingPet {

	public $height = 1.8;
	public $width = 0.72;
	public $speed = 1.1;

	public $name = "Witch Pet";
	public $tier = self::TIER_UNCOMMON;

	public $networkId = 45;
}