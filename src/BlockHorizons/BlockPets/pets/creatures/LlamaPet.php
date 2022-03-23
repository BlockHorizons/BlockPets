<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

class LlamaPet extends WalkingPet {

	const NETWORK_NAME    = "LLAMA_PET";
	const NETWORK_ORIG_ID = EntityIds::LLAMA;

	protected float $height = 0.935;
	protected float $width = 0.45;

	protected string $name = "Llama Pet";

	public function generateCustomPetData(): void {
		$randomVariant = random_int(0, 3);
		$this->getNetworkProperties()->setInt(EntityMetadataProperties::VARIANT, $randomVariant);
	}
}
