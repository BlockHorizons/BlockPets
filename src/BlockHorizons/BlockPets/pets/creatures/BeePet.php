<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;
use BlockHorizons\BlockPets\pets\SmallCreature;

class BeePet extends HoveringPet implements SmallCreature {

	const NETWORK_NAME = "BEE_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:bee";

	public $height = 0.5;
	public $width = 0.55;

	public $name = "Bee Pet";
}