<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class OcelotPet extends WalkingPet {

	public $speed = 1.4;
	public $networkId = 22;

	public $name = "Ocelot Pet";
	public $tier = self::TIER_LEGENDARY;

	public $width = 0.72;
	public $height = 0.9;
}