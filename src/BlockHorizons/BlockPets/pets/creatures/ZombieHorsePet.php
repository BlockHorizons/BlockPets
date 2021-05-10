<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class ZombieHorsePet extends WalkingPet {

	const NETWORK_NAME = "ZOMBIE_HORSE_PET";
	const NETWORK_ORIG_ID = self::ZOMBIE_HORSE;

	public $name = "Zombie Horse Pet";

	public $width = 1.3965;
	public $height = 1.6;
}
