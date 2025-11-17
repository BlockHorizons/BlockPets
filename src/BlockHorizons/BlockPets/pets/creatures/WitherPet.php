<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class WitherPet extends HoveringPet {

	public const NETWORK_NAME = "WITHER_PET";
	public const NETWORK_ORIG_ID = EntityIds::WITHER;

	protected float $height = 3.5;
	protected float $width = 0.9;

	protected string $name = "Wither Pet";
}
