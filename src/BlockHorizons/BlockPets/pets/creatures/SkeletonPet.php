<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class SkeletonPet extends WalkingPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "skeleton";
	protected const PET_NETWORK_ID = self::SKELETON;

	public $name = "Skeleton Pet";

	public $width = 0.6;
	public $height = 1.99;
}
