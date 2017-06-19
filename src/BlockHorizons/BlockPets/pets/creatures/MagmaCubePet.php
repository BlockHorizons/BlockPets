<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\BouncingPet;
use BlockHorizons\BlockPets\pets\SmallCreature;

class MagmaCubePet extends BouncingPet implements SmallCreature {

	public $height = 0.51;
	public $width = 0.51;

	public $name = "Magma Cube Pet";
	public $networkId = 42;
}