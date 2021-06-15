<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SwimmingPet;

class GuardianPet extends SwimmingPet {

	const NETWORK_NAME = "GUARDIAN_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:guardian";

	public $height = 0.85;
	public $width = 0.85;

	public $name = "Guardian Pet";
}