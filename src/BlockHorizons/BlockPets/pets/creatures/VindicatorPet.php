<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class VindicatorPet extends WalkingPet {

	const NETWORK_NAME = "VINDICATOR_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:vindicator";

	public $height = 1.95;
	public $width = 0.6;

	public $name = "Vindicator Pet";
}