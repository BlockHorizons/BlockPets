<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class HorsePet extends WalkingPet {

	public $networkId = 23;

	public $name = "Horse Pet";

	public $width = 1.4;
	public $height = 1.6;

	public function generateCustomPetData(): void {
		$variants = [
			0, 1, 2, 3, 4, 5, 6,
			256, 257, 258, 259, 260, 261, 262,
			512, 513, 514, 515, 516, 517, 518,
			768, 769, 770, 771, 772, 773, 774,
			1024, 1025, 1026, 1027, 1028, 1029, 1030
		];
		$randomVariant = $variants[array_rand($variants)];
		$this->setDataProperty(self::DATA_VARIANT, self::DATA_TYPE_INT, $randomVariant);
	}
}