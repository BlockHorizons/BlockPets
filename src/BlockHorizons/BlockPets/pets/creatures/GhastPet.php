<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;

class GhastPet extends HoveringPet {

	const NETWORK_NAME = "GHAST_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:ghast";

	public $height = 4.0;
	public $width = 4.0;

	public $name = "Ghast Pet";
}
