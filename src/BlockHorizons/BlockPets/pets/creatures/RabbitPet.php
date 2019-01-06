<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\BouncingPet;
use BlockHorizons\BlockPets\pets\SmallCreature;

class RabbitPet extends BouncingPet implements SmallCreature {

	const NETWORK_NAME = "RABBIT_PET";
	const NETWORK_ORIG = self::RABBIT;

	public $height = 0.5;
	public $width = 0.4;

	public $name = "Rabbit Pet";

	public function generateCustomPetData(): void {
		parent::generateCustomPetData();
		$variants = [
			0, 1, 2, 3, 4, 5, 99
		];
		$randomVariant = $variants[array_rand($variants)];
		$this->getDataPropertyManager()->setInt(self::DATA_VARIANT, $randomVariant);
	}
}
