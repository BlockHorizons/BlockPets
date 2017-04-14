<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class WitherSkeletonPet extends WalkingPet {

	public $height = 2.2;
	public $width = 0.8;
	public $speed = 1.4;

	public $name = "Wither Skeleton Pet";
	public $tier = self::TIER_SPECIAL;

	public $networkId = 48;
}