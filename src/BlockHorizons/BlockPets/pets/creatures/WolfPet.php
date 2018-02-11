<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\Player;

class WolfPet extends WalkingPet implements SmallCreature {

	public $networkId = 14;
	public $name = "Wolf Pet";

	public $width = 0.72;
	public $height = 0.9;

	public function generateCustomPetData(): void {

			$randomColour = random_int(0, 15);
			$eid = 123456789123456789;
			$this->propertyManager->setPropertyValue(self::DATA_OWNER_EID, self::DATA_TYPE_LONG, $eid);
			$this->propertyManager->setPropertyValue(self::DATA_COLOUR, self::DATA_TYPE_BYTE, $randomColour);
		}
}