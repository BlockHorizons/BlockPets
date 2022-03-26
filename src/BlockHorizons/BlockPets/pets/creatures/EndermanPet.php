<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class EndermanPet extends WalkingPet {

	const NETWORK_NAME = "ENDERMAN_PET";
	const NETWORK_ORIG_ID = EntityIds::ENDERMAN;

	protected float $height = 2.9;
	protected float $width = 0.6;

	protected string $name = "Enderman Pet";
}
