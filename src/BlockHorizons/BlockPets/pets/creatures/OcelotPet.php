<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

class OcelotPet extends WalkingPet implements SmallCreature {

	public $networkId = 22;
	public $name = "Ocelot Pet";

	public $width = 0.72;
	public $height = 0.9;

	public function generateCustomPetData() {
		$randomVariant = mt_rand(0, 3);
		$this->setDataProperty(self::DATA_VARIANT, self::DATA_TYPE_INT, $randomVariant);
	}
}