<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

class EndermitePet extends WalkingPet implements SmallCreature {

	public $height = 0.2;
	public $width = 0.4;
	public $speed = 1.4;

	public $name = "Endermite Pet";
	public $tier = self::TIER_SPECIAL;

	public $networkId = 55;
}