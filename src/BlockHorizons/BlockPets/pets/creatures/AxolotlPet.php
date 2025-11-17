<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class AxolotlPet extends WalkingPet implements SmallCreature {

	public const NETWORK_NAME = "AXOLOTL_PET";
	public const NETWORK_ORIG_ID = EntityIds::AXOLOTL;

	protected string $name = "Axolotl Pet";

	protected float $width = 1.3;
	protected float $height = 0.6;

}