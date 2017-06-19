<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

class SilverFishPet extends WalkingPet implements SmallCreature {

	public $height = 0.2;
	public $width = 0.4;

	public $name = "Silverfish Pet";
	public $networkId = 39;
}