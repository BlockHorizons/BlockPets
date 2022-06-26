<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class WardenPet extends WalkingPet {

	const NETWORK_NAME = "WARDEN_PET";
	const NETWORK_ORIG_ID = self::WARDEN;

	protected string $name = "Warden Pet";

	protected float $width = 2.9;
	protected float $height = 0.9;

}