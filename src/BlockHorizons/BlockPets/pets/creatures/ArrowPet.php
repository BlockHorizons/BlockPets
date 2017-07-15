<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;
use BlockHorizons\BlockPets\pets\SmallCreature;

class ArrowPet extends HoveringPet implements SmallCreature {

	public $networkId = 80;
	public $name = "Arrow Pet";

	public $width = 0.5;
	public $height = 0.5;

	public function setCritical(bool $value = true) {
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_CRITICAL, $value);
	}
}