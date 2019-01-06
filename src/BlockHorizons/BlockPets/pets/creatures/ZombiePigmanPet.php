<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class ZombiePigmanPet extends WalkingPet {

	const NETWORK_NAME = "ZOMBIE_PIGMAN_PET";
	const NETWORK_ORIG = self::ZOMBIE_PIGMAN;

	public $height = 1.95;
	public $width = 0.6;

	public $name = "Zombie Pigman Pet";
}
