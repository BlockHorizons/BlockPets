<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;
use BlockHorizons\BlockPets\pets\SmallCreature;

class ParrotPet extends HoveringPet implements SmallCreature {

	const NETWORK_NAME = "PARROT_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:parrot";

	public $height = 1.0;
	public $width = 0.5;

	public $name = "Parrot Pet";

	public function generateCustomPetData(): void {
		$randomVariant = random_int(0, 4);
		$this->getDataPropertyManager()->setInt(self::DATA_VARIANT, $randomVariant);
	}
}