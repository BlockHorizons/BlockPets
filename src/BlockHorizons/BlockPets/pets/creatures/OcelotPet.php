<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

class OcelotPet extends WalkingPet implements SmallCreature {

	const NETWORK_NAME = "OCELOT_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:ocelot";

	public $height = 0.7;
	public $width = 0.6;

	public $name = "Ocelot Pet";

	public function generateCustomPetData(): void {
		$randomVariant = random_int(0, 3);
		$this->getDataPropertyManager()->setInt(self::DATA_VARIANT, $randomVariant);
	}
}
