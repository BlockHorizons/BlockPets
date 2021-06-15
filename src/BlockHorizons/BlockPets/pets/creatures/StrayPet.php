<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class StrayPet extends WalkingPet {

	const NETWORK_NAME = "STRAY_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:stray";

	public $height = 1.99;
	public $width = 0.6;

	public $name = "Stray Pet";
}
