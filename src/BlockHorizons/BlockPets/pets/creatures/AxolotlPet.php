<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

class AxolotlPet extends WalkingPet implements SmallCreature {

	const NETWORK_NAME = "AXOLOT_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:axolotl";

	public $height = 0.3;
	public $width = 0.4;

	public $name = "Axolotl Pet";

	public function generateCustomPetData(): void {
		if( (mt_rand(1, 4800) <= 1203)){ //~0.083%
			$this->getDataPropertyManager()->setInt(self::DATA_VARIANT, 4);
		} else {
			$randomVariant = random_int(0, 3); //~24.98%
			$this->getDataPropertyManager()->setInt(self::DATA_VARIANT, $randomVariant);
		}
	}
}
