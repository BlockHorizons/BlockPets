<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\BouncingPet;
use BlockHorizons\BlockPets\pets\SmallCreature;

class SlimePet extends BouncingPet implements SmallCreature {

	const NETWORK_NAME = "SLIME_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:slime";

	public $height = 0.51;
	public $width = 0.51;

	public $name = "Slime Pet";
}