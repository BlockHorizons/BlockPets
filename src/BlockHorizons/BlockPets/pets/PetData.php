<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets;

use pocketmine\player\Player;
use pocketmine\Server;

class PetData {

	public function __construct(
		private string $petId,
		private string $ownerName,
		private string $petName,
		private float $scale = 1.0,
		private bool $isBaby = false,
		private int $level = 1,
		private int $levelPoints = 0,
		private bool $chested = false,
		private bool $isVisible = true,
		private ?string $inventory = null
	) {
	}

	public function getPetId(): string {
		return $this->petId;
	}

	public function getOwnerName(): string {
		return $this->ownerName;
	}

	public function getOwner(): ?Player {
		return Server::getInstance()->getPlayerExact($this->ownerName);
	}

	public function getPetName(): string {
		return $this->petName;
	}

	public function setPetName(string $petName): PetData {
		$this->petName = $petName;
		return $this;
	}

	public function getScale(): float {
		return $this->scale;
	}

	public function setScale(float $scale): PetData {
		$this->scale = $scale;
		return $this;
	}

	public function isBaby(): bool {
		return $this->isBaby;
	}

	public function setBaby(bool $isBaby): PetData {
		$this->isBaby = $isBaby;
		return $this;
	}

	public function getLevel(): int {
		return $this->level;
	}

	public function setLevel(int $level): PetData {
		$this->level = $level;
		return $this;
	}

	public function getLevelPoints(): int {
		return $this->levelPoints;
	}

	public function setLevelPoints(int $levelPoints): PetData {
		$this->levelPoints = $levelPoints;
		return $this;
	}

	public function isChested(): bool {
		return $this->chested;
	}

	public function setChested(bool $chested): PetData {
		$this->chested = $chested;
		return $this;
	}

	public function isVisible(): bool {
		return $this->isVisible;
	}

	public function setVisible(bool $isVisible): PetData {
		$this->isVisible = $isVisible;
		return $this;
	}

	public function getInventory(): ?string {
		return $this->inventory;
	}

	public function setInventory(?string $inventory): PetData {
		$this->inventory = $inventory;
		return $this;
	}
}