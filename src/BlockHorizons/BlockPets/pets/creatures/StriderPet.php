<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class StriderPet extends WalkingPet {

	const NETWORK_NAME = "STRIDER_PET";
	const NETWORK_ORIG_ID = EntityIds::STRIDER;

	protected string $name = "Strider Pet";

	protected float $width = 0.9;
	protected float $height = 1.7;
}