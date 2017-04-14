<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class ZombieVillagerPet extends WalkingPet {

	public $height = 1.8;
	public $width = 0.9;
	public $speed = 1.2;

	public $name = "Zombie Villager Pet";
	public $tier = self::TIER_UNCOMMON;

	public $networkId = 44;
}