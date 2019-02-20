<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;
use BlockHorizons\BlockPets\pets\SmallCreature;

class BatPet extends HoveringPet implements SmallCreature {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "bat";
	protected const PET_NETWORK_ID = self::BAT;

	public $name = "Bat Pet";

	public $width = 0.5;
	public $height = 0.9;
}
