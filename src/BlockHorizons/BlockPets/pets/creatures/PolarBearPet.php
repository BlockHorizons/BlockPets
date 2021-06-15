<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class PolarBearPet extends WalkingPet {

	const NETWORK_NAME = "POLAR_BEAR_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:polar_bear";

	public $height = 1.4;
	public $width = 1.3;

	public $name = "Polar Bear Pet";
}