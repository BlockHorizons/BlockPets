<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class PandaPet extends WalkingPet {

	const NETWORK_NAME = "PANDA_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:panda";

	public $width = 1.5;
	public $height = 1.7;

	public $name = "Panda Pet";
}
