<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class ZombieVillagerPet extends WalkingPet {

	const NETWORK_ID = self::ZOMBIE_VILLAGER;

	public $height = 1.8;
	public $width = 0.9;

	public $name = "Zombie Villager Pet";
}