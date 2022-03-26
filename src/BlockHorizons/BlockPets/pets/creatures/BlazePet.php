<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class BlazePet extends HoveringPet {

	const NETWORK_NAME = "BLAZE_PET";
	const NETWORK_ORIG_ID = EntityIds::BLAZE;

	protected string $name = "Blaze Pet";

	protected float $width = 0.6;
	protected float $height = 1.8;
}
