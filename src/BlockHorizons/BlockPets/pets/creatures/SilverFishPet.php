<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

class SilverFishPet extends WalkingPet implements SmallCreature {

	const NETWORK_NAME = "SILVERFISH_PET";
	const NETWORK_ORIG_ID = self::SILVERFISH;

	public $height = 0.3;
	public $width = 0.4;

	public $name = "SilverFish Pet";
}
