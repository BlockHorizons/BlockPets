<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SwimmingPet;

class DolphinPet extends SwimmingPet {

	const NETWORK_NAME = "DOLPHIN_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:dolphin";

	public $height = 0.5;
	public $width = 0.9;

	public $name = "Dolphin Pet";
}