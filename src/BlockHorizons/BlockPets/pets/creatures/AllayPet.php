<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;
use BlockHorizons\BlockPets\pets\SmallCreature;

class AllayPet extends HoveringPet implements SmallCreature {

	const NETWORK_NAME = "ALLAY_PET";
	const NETWORK_ORIG_ID = self::ALLAY;
	const NETWORD_ID = self::ALLAY;

	protected string $name = "Allay Pet";

	protected float $width = 0.6;
	protected float $height = 0.6;

}