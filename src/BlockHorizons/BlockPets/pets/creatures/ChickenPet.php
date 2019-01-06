<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

class ChickenPet extends WalkingPet implements SmallCreature {

	const NETWORK_NAME = "CHICKEN_PET";
	const NETWORK_ORIG = self::CHICKEN;

	public $width = 0.4;
	public $height = 0.7;

	public $name = "Chicken Pet";
}