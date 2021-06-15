<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class ZombiePigmanPet extends WalkingPet {

	const NETWORK_NAME = "ZOMBIE_PIGMAN_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:zombie_pigman";

	public $height = 1.95;
	public $width = 0.6;

	public $name = "Zombie Pigman Pet";
}
