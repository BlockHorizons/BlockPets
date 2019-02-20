<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SwimmingPet;

class SquidPet extends SwimmingPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "squid";
	protected const PET_NETWORK_ID = self::SQUID;

	public $width = 0.8;
	public $height = 0.8;

	public $name = "Squid Pet";
}
