<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\BouncingPet;
use BlockHorizons\BlockPets\pets\SmallCreature;

class RabbitPet extends BouncingPet implements SmallCreature {

	public $height = 0.5;
	public $width = 0.4;
	public $speed = 1.6;

	public $name = "Rabbit Pet";
	public $tier = self::TIER_EPIC;

	public $networkId = 18;

	public function generateCustomPetData() {
		$variants = [
			0, 1, 2, 3, 4, 5, 99
		];
		$randomVariant = $variants[array_rand($variants)];
		$this->setDataProperty(self::DATA_VARIANT, self::DATA_TYPE_INT, $randomVariant);
	}
}