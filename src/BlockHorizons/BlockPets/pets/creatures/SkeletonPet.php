<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class SkeletonPet extends WalkingPet {

	public $networkId = 34;
	public $name = "Skeleton Pet";
	public $tier = self::TIER_COMMON;

	public $width = 0.65;
	public $height = 1.8;
}