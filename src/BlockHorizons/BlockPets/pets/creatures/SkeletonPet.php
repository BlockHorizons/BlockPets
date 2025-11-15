<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class SkeletonPet extends WalkingPet {

	public const NETWORK_NAME = "SKELETON_PET";
	public const NETWORK_ORIG_ID = EntityIds::SKELETON;

	protected string $name = "Skeleton Pet";

	protected float $width = 0.6;
	protected float $height = 1.99;
}
