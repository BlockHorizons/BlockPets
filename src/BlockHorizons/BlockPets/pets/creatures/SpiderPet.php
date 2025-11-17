<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;

class SpiderPet extends WalkingPet implements SmallCreature {

	public const NETWORK_NAME = "SPIDER_PET";
	public const NETWORK_ORIG_ID = EntityIds::SPIDER;

	protected float $height = 0.9;
	protected float $width = 1.4;

	protected string $name = "Spider Pet";

	public function generateCustomPetData(): void {
		$this->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::CAN_CLIMB, true);
	}
}
