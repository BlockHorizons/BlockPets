<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;

class EnderDragonPet extends HoveringPet {

	const NETWORK_NAME = "ENDER_DRAGON_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:ender_dragon";

	public $height = 1;
	public $width = 2.5;

	public $name = "Ender Dragon Pet";
}
