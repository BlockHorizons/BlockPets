<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SwimmingPet;

class GuardianPet extends SwimmingPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "guardian";
	protected const PET_NETWORK_ID = self::GUARDIAN;

	public $width = 0.85;
	public $height = 0.85;

	public $name = "Guardian Pet";
}
