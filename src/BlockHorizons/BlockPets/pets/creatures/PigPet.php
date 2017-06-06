<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

class PigPet extends WalkingPet implements SmallCreature {

	public $height = 0.9;
	public $width = 0.7;

	public $name = "Pig Pet";

	public $networkId = 12;
}