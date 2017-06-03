<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

class CaveSpiderPet extends WalkingPet implements SmallCreature {

	public $speed = 1.2;
	public $height = 0.8;
	public $width = 0.9;

	public $name = "Cave Spider Pet";
	public $tier = self::TIER_UNCOMMON;

	public $networkId = 40;
}