<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;

class EnderCrystalPet extends HoveringPet {

	const NETWORK_NAME = "ENDER_CRYSTAL_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:ender_crystal";

	public $height = 0.8;
	public $width = 0.8;

	public $name = "Ender Crystal Pet";
}