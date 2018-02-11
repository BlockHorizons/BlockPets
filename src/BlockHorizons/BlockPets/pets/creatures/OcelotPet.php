<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

class OcelotPet extends WalkingPet implements SmallCreature {

	public $networkId = 22;
	public $name = "Ocelot Pet";

	public $width = 0.72;
	public $height = 0.9;

	public function generateCustomPetData(): void {
		$randomVariant = random_int(0, 3);
		$this->propertyManager->setPropertyValue(self::DATA_VARIANT, self::DATA_TYPE_INT, $randomVariant);
	}
}