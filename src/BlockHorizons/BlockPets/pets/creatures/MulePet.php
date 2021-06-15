<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class MulePet extends WalkingPet {

	const NETWORK_NAME = "MULE_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:mule";

	public $height = 1.6;
	public $width = 1.3965;

	public $name = "Mule Pet";
}
