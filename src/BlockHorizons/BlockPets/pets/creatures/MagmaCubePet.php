<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\BouncingPet;
use BlockHorizons\BlockPets\pets\SmallCreature;

class MagmaCubePet extends BouncingPet implements SmallCreature {

	const NETWORK_NAME = "MAGMA_CUBE_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:magma_cube";

	public $height = 0.51;
	public $width = 0.51;

	public $name = "Magma Cube Pet";
}