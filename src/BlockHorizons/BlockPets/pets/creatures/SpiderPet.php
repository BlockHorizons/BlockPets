<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

class SpiderPet extends WalkingPet implements SmallCreature {

	public $height = 1.12;
	public $width = 1.3;
	public $speed = 1.4;

	public $name = "Spider Pet";
	public $tier = self::TIER_SPECIAL;

	public $networkId = 35;
}