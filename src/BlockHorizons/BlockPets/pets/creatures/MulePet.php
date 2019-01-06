<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class MulePet extends WalkingPet {

	const NETWORK_NAME = "MULE_PET";
	const NETWORK_ORIG = self::MULE;

	public $name = "Mule Pet";

	public $width = 1.3965;
	public $height = 1.6;
}
