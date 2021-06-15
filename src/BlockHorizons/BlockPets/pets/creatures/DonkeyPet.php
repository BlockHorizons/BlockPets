<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class DonkeyPet extends WalkingPet {

	const NETWORK_NAME = "DONKEY_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:donkey";

	public $height = 1.6;
	public $width = 1.4;

	public $name = "Donkey Pet";
}
