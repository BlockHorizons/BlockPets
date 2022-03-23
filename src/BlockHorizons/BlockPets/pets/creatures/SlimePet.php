<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\BouncingPet;
use BlockHorizons\BlockPets\pets\SmallCreature;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class SlimePet extends BouncingPet implements SmallCreature {

	const NETWORK_NAME = "SLIME_PET";
	const NETWORK_ORIG_ID = EntityIds::SLIME;

	protected float $height = 0.51;
	protected float $width = 0.51;

	protected string $name = "Slime Pet";
}