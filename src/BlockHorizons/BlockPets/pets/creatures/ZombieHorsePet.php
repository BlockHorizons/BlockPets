<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class ZombieHorsePet extends WalkingPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "zombie_horse";
	protected const PET_NETWORK_ID = self::ZOMBIE_HORSE;

	public $name = "Zombie Horse Pet";

	public $width = 1.3965;
	public $height = 1.6;
}
