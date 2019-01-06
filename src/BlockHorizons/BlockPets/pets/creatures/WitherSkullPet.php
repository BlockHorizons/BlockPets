<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;
use BlockHorizons\BlockPets\pets\SmallCreature;

class WitherSkullPet extends HoveringPet implements SmallCreature {

	const NETWORK_NAME = "WITHER_SKULL_PET";
	const NETWORK_ORIG = self::WITHER_SKULL;

	public $height = 0.4;
	public $width = 0.4;

	public $name = "Wither Skull Pet";
}
