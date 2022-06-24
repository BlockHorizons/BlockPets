<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

class FoxPet extends WalkingPet implements SmallCreature {

	const NETWORK_NAME = "FOX_PET";
	const NETWORK_ORIG_ID = self::FOX;
	const NETWORD_ID = self::FOX;

	protected string $name = "Fox Pet";

	protected float $width = 0.6;
	protected float $height = 0.7;

}