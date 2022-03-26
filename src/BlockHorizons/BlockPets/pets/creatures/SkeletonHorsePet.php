<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class SkeletonHorsePet extends WalkingPet {

	const NETWORK_NAME = "SKELETON_HORSE_PET";
	const NETWORK_ORIG_ID = EntityIds::SKELETON_HORSE;

	protected string $name = "Skeleton Horse Pet";

	protected float $width = 1.3965;
	protected float $height = 1.6;
}
