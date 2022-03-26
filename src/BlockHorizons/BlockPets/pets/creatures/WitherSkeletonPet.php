<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class WitherSkeletonPet extends WalkingPet {

	const NETWORK_NAME = "WITHER_SKELETON_PET";
	const NETWORK_ORIG_ID = EntityIds::WITHER_SKELETON;

	protected float $height = 2.4;
	protected float $width = 0.7;

	protected string $name = "Wither Skeleton Pet";
}
