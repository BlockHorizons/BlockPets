<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class GoatPet extends WalkingPet {

	public const NETWORK_NAME = "GOAT_PET";
	public const NETWORK_ORIG_ID = EntityIds::GOAT;

	protected string $name = "Goat Pet";

	protected float $width = 0.9;
	protected float $height = 1.3;

}