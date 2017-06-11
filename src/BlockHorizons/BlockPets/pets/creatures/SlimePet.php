<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\BouncingPet;
use BlockHorizons\BlockPets\pets\SmallCreature;

class SlimePet extends BouncingPet implements SmallCreature {

	public $height = 0.51;
	public $width = 0.51;
	public $speed = 1.2;

	public $name = "Slime Pet";
	public $tier = self::TIER_UNCOMMON;

	public $networkId = 37;
}