<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

class AxolotlPet extends WalkingPet implements SmallCreature {

	const NETWORK_NAME = "AXOLOTL_PET";
	const NETWORK_ORIG_ID = self::AXOLOTL;
	const NETWORD_ID = self::AXOLOTL;

	protected string $name = "Axolotl Pet";

	protected float $width = 1.3;
	protected float $height = 0.6;

}