<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;

class BlazePet extends HoveringPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "blaze";
	protected const PET_NETWORK_ID = self::BLAZE;

	public $width = 0.6;
	public $height = 1.8;

	public $name = "Blaze Pet";
}
