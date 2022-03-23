<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;
use BlockHorizons\BlockPets\pets\SmallCreature;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class BatPet extends HoveringPet implements SmallCreature {

	const NETWORK_NAME = "BAT_PET";
	const NETWORK_ORIG_ID = EntityIds::BAT;

	protected string $name = "Bat Pet";

	protected float $width = 0.5;
	protected float $height = 0.9;
}