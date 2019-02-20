<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;

class GhastPet extends HoveringPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "ghast";
	protected const PET_NETWORK_ID = self::GHAST;

	public $width = 4.0;
	public $height = 4.0;

	public $name = "Ghast Pet";
}
