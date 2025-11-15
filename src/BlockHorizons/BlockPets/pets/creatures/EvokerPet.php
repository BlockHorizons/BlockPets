<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;

class EvokerPet extends WalkingPet {

	public const NETWORK_NAME = "EVOKER_PET";
	public const NETWORK_ORIG_ID = EntityIds::EVOCATION_ILLAGER;

	protected string $name = "Evoker Pet";

	protected float $width = 0.6;
	protected float $height = 1.95;

	public function generateCustomPetData(): void {
		$isCasting = random_int(0, 1);
		$this->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::EVOKER_SPELL, (bool) $isCasting);
	}
}