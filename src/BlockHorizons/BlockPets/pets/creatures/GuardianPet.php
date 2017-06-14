<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SwimmingPet;

class GuardianPet extends SwimmingPet {

	public $width = 0.85;
	public $height = 0.85;

	public $name = "Guardian Pet";
	public $swimmingSpeed = 1.8;

	public $networkId = 49;

	protected $tier = self::TIER_EPIC;
}