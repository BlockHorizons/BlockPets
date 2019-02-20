<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

class ChickenPet extends WalkingPet implements SmallCreature {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "chicken";
	protected const PET_NETWORK_ID = self::CHICKEN;

	public $width = 0.4;
	public $height = 0.7;

	public $name = "Chicken Pet";
}
