<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;

class EnderCrystalPet extends HoveringPet {

	const NETWORK_NAME = "ENDER_CRYSTAL_PET";
	const NETWORK_ORIG = self::ENDER_CRYSTAL;

	public $width = 0.8;
	public $height = 0.8;

	public $name = "Ender Crystal Pet";
}
