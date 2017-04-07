<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class ChickenPet extends WalkingPet {

	public $width = 0.4;
	public $height = 0.7;

	public $name = "Chicken Pet";

	public function getSpeed(): float {
		return 1.0;
	}

	public function getNetworkId(): int {
		return 10;
	}
}