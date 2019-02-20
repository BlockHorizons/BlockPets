<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class SkeletonHorsePet extends WalkingPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "skeleton_horse";
	protected const PET_NETWORK_ID = self::SKELETON_HORSE;

	public $name = "Skeleton Horse Pet";

	public $width = 1.3965;
	public $height = 1.6;
}
