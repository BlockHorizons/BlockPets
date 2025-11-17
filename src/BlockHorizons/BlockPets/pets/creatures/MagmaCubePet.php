<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\BouncingPet;
use BlockHorizons\BlockPets\pets\SmallCreature;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class MagmaCubePet extends BouncingPet implements SmallCreature {

	public const NETWORK_NAME = "MAGMA_CUBE_PET";
	public const NETWORK_ORIG_ID = EntityIds::MAGMA_CUBE;

	protected float $height = 0.51;
	protected float $width = 0.51;

	protected string $name = "Magma Cube Pet";
}