<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class MulePet extends WalkingPet {

	public $speed = 1.6;
	public $networkId = 25;

	public $name = "Mule Pet";
	public $tier = self::TIER_EPIC;

	public $width = 1.4;
	public $height = 1.6;
}