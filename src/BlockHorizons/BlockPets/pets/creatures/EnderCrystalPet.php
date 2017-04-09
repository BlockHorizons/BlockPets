<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;

class EnderCrystalPet extends HoveringPet {

	public $width = 0.8;
	public $height = 0.8;

	public $name = "Ender Crystal Pet";

	public $speed = 0.8;
	public $networkId = 71;

	protected $flyHeight = 13;
	protected $tier = self::TIER_UNCOMMON;
}