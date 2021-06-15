<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class ZombiePet extends WalkingPet {

	const NETWORK_NAME = "ZOMBIE_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:zombie";

	public $height = 1.95;
	public $width = 0.6;

	public $name = "Zombie Pet";
}
