<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class WardenPet extends WalkingPet {

	public const NETWORK_NAME = "WARDEN_PET";
	public const NETWORK_ORIG_ID = EntityIds::WARDEN;

	protected string $name = "Warden Pet";

	protected float $width = 2.9;
	protected float $height = 0.9;

}