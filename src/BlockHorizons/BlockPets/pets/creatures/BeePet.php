<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;
use BlockHorizons\BlockPets\pets\SmallCreature;

class BeePet extends HoveringPet implements SmallCreature {

	const NETWORK_NAME = "BEE_PET";
	const NETWORK_ORIG_ID = self::BEE;

	protected string $name = "Bee Pet";

	protected float $width = 0.55;
	protected float $height = 0.5;

}