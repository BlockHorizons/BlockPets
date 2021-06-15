<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class SkeletonPet extends WalkingPet {

	const NETWORK_NAME = "SKELETON_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:skeleton";

	public $height = 1.99;
	public $width = 0.6;

	public $name = "Skeleton Pet";
}
