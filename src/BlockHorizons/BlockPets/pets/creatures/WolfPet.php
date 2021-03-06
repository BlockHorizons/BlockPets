<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

class WolfPet extends WalkingPet implements SmallCreature {

	const NETWORK_NAME = "WOLF_PET";
	const NETWORK_ORIG_ID = self::WOLF;

	public $name = "Wolf Pet";

	public $width = 0.6;
	public $height = 0.85;

	public function generateCustomPetData(): void {
		$randomColour = random_int(0, 15);
		$eid = 123456789123456789;
		$this->getDataPropertyManager()->setLong(self::DATA_OWNER_EID, $eid);
		$this->getDataPropertyManager()->setByte(self::DATA_COLOUR, $randomColour);
	}
}
