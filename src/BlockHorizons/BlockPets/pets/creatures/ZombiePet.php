<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class ZombiePet extends WalkingPet {

	const NETWORK_ID = self::ZOMBIE;

	public $name = "Zombie Pet";

	public $width = 0.72;
	public $height = 1.8;
}