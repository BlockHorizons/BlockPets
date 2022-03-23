<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class EnderDragonPet extends HoveringPet {

	const NETWORK_NAME = "ENDER_DRAGON_PET";
	const NETWORK_ORIG_ID = EntityIds::ENDER_DRAGON;

	protected string $name = "Ender Dragon Pet";

	protected float $width = 2.5;
	protected float $height = 1;
}
