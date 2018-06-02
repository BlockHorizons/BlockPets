<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class WitherSkeletonPet extends WalkingPet {

	const NETWORK_ID = self::WITHER_SKELETON;

	public $height = 2.2;
	public $width = 0.8;

	public $name = "Wither Skeleton Pet";
}