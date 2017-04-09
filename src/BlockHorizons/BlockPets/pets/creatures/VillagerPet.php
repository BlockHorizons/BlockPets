<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class VillagerPet extends WalkingPet {

	public $height = 1.8;
	public $width = 0.8;

	public $name = "Villager Pet";
	public $tier = self::TIER_COMMON;

	public $networkId = 15;
}