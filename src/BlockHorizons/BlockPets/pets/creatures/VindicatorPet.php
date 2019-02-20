<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class VindicatorPet extends WalkingPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "vindicator";
	protected const PET_NETWORK_ID = self::VINDICATOR;

	public $name = "Vindicator Pet";

	public $width = 0.6;
	public $height = 1.95;
}
