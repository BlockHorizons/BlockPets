<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;
use BlockHorizons\BlockPets\pets\SmallCreature;

class WitherSkullPet extends HoveringPet implements SmallCreature {

	public $height = 0.4;
	public $width = 0.4;

	public $name = "Wither Skull Pet";
	public $networkId = 89;
}