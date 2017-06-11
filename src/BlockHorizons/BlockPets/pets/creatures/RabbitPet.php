<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\BouncingPet;
use BlockHorizons\BlockPets\pets\SmallCreature;

class RabbitPet extends BouncingPet implements SmallCreature {

	public $height = 0.5;
	public $width = 0.4;
	public $speed = 1.6;

	public $name = "Rabbit Pet";
	public $tier = self::TIER_EPIC;

	public $networkId = 18;
}