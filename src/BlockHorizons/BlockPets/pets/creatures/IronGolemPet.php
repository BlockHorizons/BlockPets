<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class IronGolemPet extends WalkingPet {

	const NETWORK_NAME = "IRON_GOLEM_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:iron_golem";

	public $height = 2.7;
	public $width = 1.4;

	public $name = "Iron Golem Pet";
}
