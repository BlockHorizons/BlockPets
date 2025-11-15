<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;
use BlockHorizons\BlockPets\pets\SmallCreature;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class AllayPet extends HoveringPet implements SmallCreature {

	public const NETWORK_NAME = "ALLAY_PET";
	public const NETWORK_ORIG_ID = EntityIds::ALLAY;

	protected string $name = "Allay Pet";

	protected float $width = 0.6;
	protected float $height = 0.6;

}