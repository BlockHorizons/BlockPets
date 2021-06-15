<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

use pocketmine\entity\Entity;

class FoxRedPet extends WalkingPet implements SmallCreature {

	const NETWORK_NAME = "FOX_RED_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:fox";

	public $name = "Fox Red Pet";

	public $width = 0.7;
	public $height = 0.6;
	
	public function generateCustomPetData(): void {
		$this->getDataPropertyManager()->setInt(self::DATA_VARIANT, 0);
	}
	
}
	