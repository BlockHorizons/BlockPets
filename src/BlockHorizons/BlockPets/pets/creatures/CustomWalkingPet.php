<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HumanWalkingPet;

class CustomWalkingPet extends HumanWalkingPet {

	const NETWORK_NAME = "CUSTOM_WALKING_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:player";

	public $height = 1.8;
	public $width = 0.6;

	public $name = "Custom Walking Pet";
}
