<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class CowPet extends WalkingPet {

	const NETWORK_NAME = "COW_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:cow";

	public $height = 1.4;
	public $width = 0.9;

	public $name = "Cow Pet";
}
