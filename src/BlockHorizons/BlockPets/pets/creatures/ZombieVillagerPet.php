<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class ZombieVillagerPet extends WalkingPet {

	const NETWORK_NAME = "ZOMBIE_VILLAGER_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:zombie_villager";

	public $height = 1.95;
	public $width = 0.6;

	public $name = "Zombie Villager Pet";
}
