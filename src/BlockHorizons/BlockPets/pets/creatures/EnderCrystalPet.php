<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;

class EnderCrystalPet extends HoveringPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "ender_crystal";
	protected const PET_NETWORK_ID = self::ENDER_CRYSTAL;

	public $width = 0.8;
	public $height = 0.8;

	public $name = "Ender Crystal Pet";
}
