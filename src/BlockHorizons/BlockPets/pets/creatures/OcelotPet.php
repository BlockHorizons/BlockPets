<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;

class OcelotPet extends WalkingPet implements SmallCreature {

	public $speed = 1.8;
	public $networkId = 22;

	public $name = "Ocelot Pet";
	public $tier = self::TIER_LEGENDARY;

	public $width = 0.72;
	public $height = 0.9;
}