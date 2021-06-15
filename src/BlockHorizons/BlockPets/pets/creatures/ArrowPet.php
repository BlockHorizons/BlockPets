<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;
use BlockHorizons\BlockPets\pets\SmallCreature;

class ArrowPet extends HoveringPet implements SmallCreature {

	const NETWORK_NAME = "ARROW_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:arrow";

	public $height = 0.5;
	public $width = 0.5;

	public $name = "Arrow Pet";

	public function setCritical(bool $value = true): void {
		$this->setGenericFlag(self::DATA_FLAG_CRITICAL, $value);
	}
}