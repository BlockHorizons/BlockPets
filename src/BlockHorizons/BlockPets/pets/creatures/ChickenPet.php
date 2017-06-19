<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

class ChickenPet extends WalkingPet implements SmallCreature {

	public $width = 0.4;
	public $height = 0.7;

	public $name = "Chicken Pet";

	public $networkId = 10;
}