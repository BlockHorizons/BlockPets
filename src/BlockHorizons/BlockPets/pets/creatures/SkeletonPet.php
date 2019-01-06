<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class SkeletonPet extends WalkingPet {

	const NETWORK_NAME = "SKELETON_PET";
	const NETWORK_ORIG = self::SKELETON;

	public $name = "Skeleton Pet";

	public $width = 0.6;
	public $height = 1.99;
}
