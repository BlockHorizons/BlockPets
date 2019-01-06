<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class ZombieVillagerPet extends WalkingPet {

	const NETWORK_NAME = "ZOMBIE_VILLAGER_PET";
	const NETWORK_ORIG = self::ZOMBIE_VILLAGER;

	public $height = 1.95;
	public $width = 0.6;

	public $name = "Zombie Villager Pet";
}
