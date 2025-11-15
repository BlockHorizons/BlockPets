<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class FoxPet extends WalkingPet implements SmallCreature {

	public const NETWORK_NAME = "FOX_PET";
	public const NETWORK_ORIG_ID = EntityIds::FOX;

	protected string $name = "Fox Pet";

	protected float $width = 0.6;
	protected float $height = 0.7;

}