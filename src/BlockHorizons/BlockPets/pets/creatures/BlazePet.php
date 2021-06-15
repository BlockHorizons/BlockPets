<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;

class BlazePet extends HoveringPet {

	const NETWORK_NAME = "BLAZE_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:blaze";

	public $height = 1.8;
	public $width = 0.6;

	public $name = "Blaze Pet";
}
