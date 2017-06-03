<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;
use BlockHorizons\BlockPets\pets\SmallCreature;

class BatPet extends HoveringPet implements SmallCreature {

	public $networkId = 19;
	public $name = "Bat Pet";

	public $width = 0.3;
	public $height = 0.3;

	protected $flyHeight = 10;
	protected $tier = self::TIER_COMMON;
}