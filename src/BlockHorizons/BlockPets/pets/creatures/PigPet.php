<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

class PigPet extends WalkingPet implements SmallCreature {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "pig";
	protected const PET_NETWORK_ID = self::PIG;

	public $height = 0.9;
	public $width = 0.9;

	public $name = "Pig Pet";
}
