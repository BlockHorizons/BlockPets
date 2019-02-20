<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;

class EnderDragonPet extends HoveringPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "ender_dragon";
	protected const PET_NETWORK_ID = self::ENDER_DRAGON;

	public $name = "Ender Dragon Pet";

	public $width = 2.5;
	public $height = 1;
}
