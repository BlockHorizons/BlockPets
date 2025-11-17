<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class GhastPet extends HoveringPet {

	public const NETWORK_NAME = "GHAST_PET";
	public const NETWORK_ORIG_ID = EntityIds::GHAST;

	protected float $width = 4.0;
	protected float $height = 4.0;

	protected string $name = "Ghast Pet";
}
