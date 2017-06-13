<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class DonkeyPet extends WalkingPet {

	public $speed = 1.4;
	public $networkId = 24;

	public $name = "Donkey Pet";
	public $tier = self::TIER_SPECIAL;

	public $width = 1.4;
	public $height = 1.6;
}