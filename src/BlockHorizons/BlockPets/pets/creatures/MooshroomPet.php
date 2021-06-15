<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class MooshroomPet extends WalkingPet {

	const NETWORK_NAME = "MOOSHROOM_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:mooshroom";

	public $height = 1.4;
	public $width = 0.9;

	public $name = "Mooshroom Pet";
}
