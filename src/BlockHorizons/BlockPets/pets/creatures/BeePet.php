<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;
use BlockHorizons\BlockPets\pets\SmallCreature;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class BeePet extends HoveringPet implements SmallCreature {

	public const NETWORK_NAME = "BEE_PET";
	public const NETWORK_ORIG_ID = EntityIds::BEE;

	protected string $name = "Bee Pet";

	protected float $width = 0.55;
	protected float $height = 0.5;

}