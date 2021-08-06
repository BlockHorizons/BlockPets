<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HumanHoveringPet;

class CustomHoveringPet extends HumanHoveringPet {

	const NETWORK_NAME = "CUSTOM_HOVERING_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:player";

	public $height = 1.8;
	public $width = 0.6;

	public $name = "Custom Hovering Pet";
}
