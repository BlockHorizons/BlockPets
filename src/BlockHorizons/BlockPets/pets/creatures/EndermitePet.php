<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

class EndermitePet extends WalkingPet implements SmallCreature {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "endermite";
	protected const PET_NETWORK_ID = self::ENDERMITE;

	public $height = 0.3;
	public $width = 0.4;

	public $name = "Endermite Pet";
}
