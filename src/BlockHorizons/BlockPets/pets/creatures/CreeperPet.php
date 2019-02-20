<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class CreeperPet extends WalkingPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "creeper";
	protected const PET_NETWORK_ID = self::CREEPER;

	public $height = 1.7;
	public $width = 0.6;

	public $name = "Creeper Pet";
}
