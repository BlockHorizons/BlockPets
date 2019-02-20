<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class WitherSkeletonPet extends WalkingPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "wither_skeleton";
	protected const PET_NETWORK_ID = self::WITHER_SKELETON;

	public $height = 2.4;
	public $width = 0.7;

	public $name = "Wither Skeleton Pet";
}
