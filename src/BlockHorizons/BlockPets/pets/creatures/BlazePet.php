<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;

class BlazePet extends HoveringPet {

	public $width = 0.72;
	public $height = 1.8;

	public $speed = 1.1;
	public $name = "Blaze Pet";

	public $networkId = 43;

	protected $flyHeight = 13;
	protected $tier = self::TIER_UNCOMMON;
}