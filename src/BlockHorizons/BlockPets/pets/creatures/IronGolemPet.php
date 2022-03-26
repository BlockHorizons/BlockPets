<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class IronGolemPet extends WalkingPet {

	const NETWORK_NAME = "IRON_GOLEM_PET";
	const NETWORK_ORIG_ID = EntityIds::IRON_GOLEM;

	protected float $height = 2.7;
	protected float $width = 1.4;

	protected string $name = "Iron Golem Pet";
}
