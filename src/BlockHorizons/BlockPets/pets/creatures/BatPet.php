<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;
use BlockHorizons\BlockPets\pets\SmallCreature;

class BatPet extends HoveringPet implements SmallCreature {

	const NETWORK_NAME = "BAT_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:bat";

	public $height = 0.9;
	public $width = 0.5;

	public $name = "Bat Pet";
}