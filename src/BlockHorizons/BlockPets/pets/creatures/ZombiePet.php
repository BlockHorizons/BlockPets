<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class ZombiePet extends WalkingPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "zombie";
	protected const PET_NETWORK_ID = self::ZOMBIE;

	public $name = "Zombie Pet";

	public $width = 0.6;
	public $height = 1.95;
}
