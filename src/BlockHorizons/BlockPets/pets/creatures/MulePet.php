<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class MulePet extends WalkingPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "mule";
	protected const PET_NETWORK_ID = self::MULE;

	public $name = "Mule Pet";

	public $width = 1.3965;
	public $height = 1.6;
}
