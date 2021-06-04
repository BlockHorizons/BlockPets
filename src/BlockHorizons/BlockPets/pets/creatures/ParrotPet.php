<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;
use BlockHorizons\BlockPets\pets\SmallCreature;

class ParrotPet extends HoveringPet implements SmallCreature {

	const NETWORK_NAME = "PARROT_PET";
	const NETWORK_ORIG_ID = self::PARROT;

	public $name = "Parrot Pet";

	public $width = 0.5;
	public $height = 1.0;
}