<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class EvocationFangsPet extends WalkingPet {

	public $height = 0.5;
	public $width = 0.25;

	public $name = "Evocation Fangs Pet";
	public $speed = 1.4;

	public $networkId = 103;

	protected $tier = self::TIER_SPECIAL;
}