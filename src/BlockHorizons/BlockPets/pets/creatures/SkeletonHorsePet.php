<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class SkeletonHorsePet extends WalkingPet {

	const NETWORK_ID = self::SKELETON_HORSE;

	public $name = "Skeleton Horse Pet";

	public $width = 1.3965;
	public $height = 1.6;
}
