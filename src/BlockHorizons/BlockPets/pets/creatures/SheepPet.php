<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

class SheepPet extends WalkingPet {

	const NETWORK_NAME = "SHEEP_PET";
	const NETWORK_ORIG_ID = EntityIds::SHEEP;

	protected float $height = 1.3;
	protected float $width = 0.9;

	protected string $name = "Sheep Pet";
	protected int $color;

	public function generateCustomPetData(): void {
		$this->setColor(random_int(0, 15));
	}

	public function setColor(int $color): void {
		$this->color = $color % 16;
		$this->getNetworkProperties()->setByte(EntityMetadataProperties::COLOR, $color % 16);
	}

	public function getColor(): int {
		return $this->color;
	}

	public function doPetUpdates(int $currentTick): bool {
		if($currentTick % 10 === 0 && $this->getPetName() === "jeb_") {
			$this->setColor($this->getColor() + 1);
		}
		return parent::doPetUpdates($currentTick);
	}
}
