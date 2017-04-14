<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class ZombiePigmanPet extends WalkingPet {

	public $height = 1.8;
	public $width = 0.72;
	public $speed = 1.2;

	public $name = "Zombie Pigman Pet";
	public $tier = self::TIER_UNCOMMON;

	public $networkId = 36;
}