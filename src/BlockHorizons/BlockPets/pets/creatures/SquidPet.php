<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SwimmingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class SquidPet extends SwimmingPet {

	const NETWORK_NAME = "SQUID_PET";
	const NETWORK_ORIG_ID = EntityIds::SQUID;

	protected float $width = 0.8;
	protected float $height = 0.8;

	protected string $name = "Squid Pet";
}