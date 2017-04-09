<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;

class EnderDragonPet extends HoveringPet {

	public $speed = 1.6;
	public $networkId = 53;
	public $name = "Ender Dragon Pet";

	public $width = 2.5;
	public $height = 1;

	protected $flyHeight = 50;
	protected $tier = self::TIER_LEGENDARY;
}
