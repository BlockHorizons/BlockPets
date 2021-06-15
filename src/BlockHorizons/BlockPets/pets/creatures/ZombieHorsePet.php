<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class ZombieHorsePet extends WalkingPet {

	const NETWORK_NAME = "ZOMBIE_HORSE_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:zombie_horse";

	public $height = 1.6;
	public $width = 1.3965;

	public $name = "Zombie Horse Pet";
}
