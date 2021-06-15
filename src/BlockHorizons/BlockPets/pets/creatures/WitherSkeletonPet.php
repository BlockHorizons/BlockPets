<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class WitherSkeletonPet extends WalkingPet {

	const NETWORK_NAME = "WITHER_SKELETON_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:wither_skeleton";

	public $height = 2.4;
	public $width = 0.7;

	public $name = "Wither Skeleton Pet";
}
