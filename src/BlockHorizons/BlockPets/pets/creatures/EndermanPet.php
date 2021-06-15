<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class EndermanPet extends WalkingPet {

	const NETWORK_NAME = "ENDERMAN_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:enderman";

	public $height = 2.9;
	public $width = 0.6;

	public $name = "Enderman Pet";
}
