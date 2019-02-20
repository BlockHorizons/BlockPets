<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\BouncingPet;
use BlockHorizons\BlockPets\pets\SmallCreature;

class SlimePet extends BouncingPet implements SmallCreature {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "slime";
	protected const PET_NETWORK_ID = self::SLIME;

	public $height = 0.51;
	public $width = 0.51;

	public $name = "Slime Pet";
}
