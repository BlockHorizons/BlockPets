<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;

class GhastPet extends HoveringPet {

	public $networkId = 41;

	public $width = 4;
	public $height = 4;

	public $name = "Ghast Pet";

	public function getSpeed(): float {
		return 1.2;
	}
}