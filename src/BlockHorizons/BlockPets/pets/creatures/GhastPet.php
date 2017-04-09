<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;

class GhastPet extends HoveringPet {

	public $width = 4;
	public $height = 4;

	public $name = "Ghast Pet";

	public $speed = 1.2;

	public $networkId = 41;

	protected $flyHeight = 25;
	protected $tier = self::TIER_SPECIAL;
}