<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;
use BlockHorizons\BlockPets\pets\SmallCreature;

class VexPet extends HoveringPet implements SmallCreature {

	const NETWORK_NAME = "VEX_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:vex";

	public $height = 0.8;
	public $width = 0.4;

	public $name = "Vex Pet";

	public function generateCustomPetData(): void {
		$this->canCollide = false;
	}
}