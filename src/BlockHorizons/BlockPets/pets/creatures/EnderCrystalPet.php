<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;

class EnderCrystalPet extends HoveringPet {

	const NETWORK_ID = self::ENDER_CRYSTAL;

	public $width = 0.8;
	public $height = 0.8;

	public $name = "Ender Crystal Pet";
}