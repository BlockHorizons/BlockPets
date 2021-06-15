<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class HuskPet extends WalkingPet {

	const NETWORK_NAME = "HUSK_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:husk";

	public $height = 1.95;
	public $width = 0.6;

	public $name = "Husk Pet";
}
