<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

class EndermitePet extends WalkingPet implements SmallCreature {

	const NETWORK_NAME = "ENDERMITE_PET";
	const NETWORK_ORIG_ID = self::ENDERMITE;

	public $height = 0.3;
	public $width = 0.4;

	public $name = "Endermite Pet";
}
