<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;

class WitherPet extends HoveringPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "wither";
	protected const PET_NETWORK_ID = self::WITHER;

	public $height = 3.5;
	public $width = 0.9;

	public $name = "Wither Pet";
}
