<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class ZombiePigmanPet extends WalkingPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "zombie_pigman";
	protected const PET_NETWORK_ID = self::ZOMBIE_PIGMAN;

	public $height = 1.95;
	public $width = 0.6;

	public $name = "Zombie Pigman Pet";
}
