<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SwimmingPet;

class TurtlePet extends SwimmingPet {

	const NETWORK_NAME = "TURTLE_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:turtle";

	public $height = 0.4;
	public $width = 1.2;

	public $name = "Turtle Pet";
}