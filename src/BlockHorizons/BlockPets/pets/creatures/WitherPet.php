<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;

class WitherPet extends HoveringPet {

	public $height = 3;
	public $width = 3;

	public $name = "Wither Pet";
	public $speed = 1.6;

	public $networkId = 52;

	protected $flyHeight = 55;
	protected $tier = self::TIER_LEGENDARY;
}