<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class HuskPet extends WalkingPet {

	const NETWORK_NAME = "HUSK_PET";
	const NETWORK_ORIG_ID = EntityIds::HUSK;

	protected float $height = 1.95;
	protected float $width = 0.6;

	protected string $name = "Husk Pet";
}
