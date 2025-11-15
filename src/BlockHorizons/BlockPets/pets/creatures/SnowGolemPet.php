<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;

class SnowGolemPet extends WalkingPet {

	public const NETWORK_NAME = "SNOW_GOLEM_PET";
	public const NETWORK_ORIG_ID = EntityIds::SNOW_GOLEM;

	protected float $height = 1.9;
	protected float $width = 0.7;

	protected string $name = "Snow Golem Pet";

	public function generateCustomPetData(): void {
		// wth ?? shoghicp 💀
		if($this->getPetName() !== "shoghicp") {
			return;
		}

		$this->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::SHEARED, true);
	}
}
