<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SwimmingPet;

class SalmonPet extends SwimmingPet {

	const NETWORK_NAME = "SALMON_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:salmon";

	public $height = 0.4;
	public $width = 0.7;

	public $name = "Salmon Pet";
}