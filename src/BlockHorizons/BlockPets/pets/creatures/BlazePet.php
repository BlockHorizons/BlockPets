<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;

class BlazePet extends HoveringPet {

	public $width = 0.72;
	public $height = 1.8;

	public $name = "Blaze Pet";

	public function getSpeed(): float {
		return 1.0;
	}

	public function getNetworkId(): int {
		return 43;
	}
}