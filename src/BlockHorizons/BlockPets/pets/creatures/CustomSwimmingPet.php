<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HumanSwimmingPet;

class CustomSwimmingPet extends HumanSwimmingPet {

	const NETWORK_NAME = "CUSTOM_SWIMMING_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:player";

	public $height = 1.8;
	public $width = 0.6;

	public $name = "Custom Swimming Pet";
}
