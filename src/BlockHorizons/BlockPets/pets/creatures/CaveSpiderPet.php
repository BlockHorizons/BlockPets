<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;

class CaveSpiderPet extends WalkingPet implements SmallCreature {

	public const NETWORK_NAME = "CAVE_SPIDER_PET";
	public const NETWORK_ORIG_ID = EntityIds::CAVE_SPIDER;

	protected string $name = "Cave Spider Pet";

	public float $speed = 1.4;
	protected float $height = 0.5;
	protected float $width = 0.7;

	public function generateCustomPetData(): void {
		$this->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::CAN_CLIMB, true);
	}
}
