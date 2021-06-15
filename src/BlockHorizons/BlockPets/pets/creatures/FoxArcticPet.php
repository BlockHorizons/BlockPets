<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

use pocketmine\entity\Entity;

class FoxArcticPet extends WalkingPet implements SmallCreature {

	const NETWORK_NAME = "FOX_ARCTIC_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:fox";

	public $name = "Fox Arctic Pet";

	public $width = 0.7;
	public $height = 0.6;
	
	public function generateCustomPetData(): void {
		$this->getDataPropertyManager()->setInt(self::DATA_VARIANT, 1);
	}
	
}
	