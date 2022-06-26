<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class GoatPet extends WalkingPet {

	const NETWORK_NAME = "GOAT_PET";
	const NETWORK_ORIG_ID = self::GOAT;

	protected string $name = "Goat Pet";

	protected float $width = 0.9;
	protected float $height = 1.3;

}