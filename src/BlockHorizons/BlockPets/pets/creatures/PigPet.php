<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

class PigPet extends WalkingPet implements SmallCreature {

	const NETWORK_NAME = "PIG_PET";
	const NETWORK_ORIG_ID = self::PIG;

	public $height = 0.9;
	public $width = 0.9;

	public $name = "Pig Pet";
}
