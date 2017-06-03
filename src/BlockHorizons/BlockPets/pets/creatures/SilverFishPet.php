<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

class SilverFishPet extends WalkingPet implements SmallCreature {

	public $height = 0.2;
	public $width = 0.4;
	public $speed = 1.2;

	public $name = "Silverfish Pet";
	public $tier = self::TIER_UNCOMMON;

	public $networkId = 39;
}