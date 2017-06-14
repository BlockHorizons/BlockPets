<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SwimmingPet;

class SquidPet extends SwimmingPet {

	public $width = 0.8;
	public $height = 0.8;

	public $name = "Squid Pet";
	public $swimmingSpeed = 1.4;

	public $networkId = 17;

	protected $tier = self::TIER_UNCOMMON;
}