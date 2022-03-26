<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class CreeperPet extends WalkingPet {

	const NETWORK_NAME = "CREEPER_PET";
	const NETWORK_ORIG_ID = EntityIds::CREEPER;

	protected float $height = 1.7;
	protected float $width = 0.6;

	protected string $name = "Creeper Pet";
}
