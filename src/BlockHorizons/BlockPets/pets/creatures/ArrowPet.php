<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;
use BlockHorizons\BlockPets\pets\SmallCreature;

class ArrowPet extends HoveringPet implements SmallCreature {

	const NETWORK_NAME = "ARROW_PET";
	const NETWORK_ORIG_ID = self::ARROW;

	public $name = "Arrow Pet";

	public $width = 0.5;
	public $height = 0.5;

	public function setCritical(bool $value = true): void {
		$this->setGenericFlag(self::DATA_FLAG_CRITICAL, $value);
	}
}