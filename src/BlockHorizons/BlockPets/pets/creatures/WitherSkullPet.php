<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;
use BlockHorizons\BlockPets\pets\SmallCreature;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class WitherSkullPet extends HoveringPet implements SmallCreature {

	const NETWORK_NAME = "WITHER_SKULL_PET";
	const NETWORK_ORIG_ID = EntityIds::WITHER_SKULL;

	protected float $height = 0.4;
	protected float $width = 0.4;

	protected string $name = "Wither Skull Pet";
}