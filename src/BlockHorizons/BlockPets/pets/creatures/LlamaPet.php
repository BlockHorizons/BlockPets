<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class LlamaPet extends WalkingPet {

	public $height = 1.87;
	public $width = 0.9;
	public $speed = 1.4;

	public $name = "Llama Pet";
	public $tier = self::TIER_SPECIAL;

	public $networkId = 29;
}