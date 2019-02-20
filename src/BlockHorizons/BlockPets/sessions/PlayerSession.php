<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\sessions;

use BlockHorizons\BlockPets\events\PetSpawnEvent;
use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;
use BlockHorizons\BlockPets\pets\PetFactory;
use BlockHorizons\BlockPets\pets\datastorage\types\PetData;
use BlockHorizons\BlockPets\pets\datastorage\types\PetOwnerData;
use BlockHorizons\BlockPets\sessions\types\PetSelectionData;
use pocketmine\entity\Entity;
use pocketmine\Player;
use pocketmine\Server;

class PlayerSession {

	/** @var PlayerSession[] */
	private static $sessions = [];

	public static function create(Loader $loader, Player $player): void {
		if(isset(self::$sessions[$id = spl_object_id($player)])) {
			throw new \RuntimeError("Attempted to create PlayerSession for a player already having a PlayerSession.");
		}

		self::$sessions[$id] = new PlayerSession($loader, $player);
	}

	public static function get(Player $player): PlayerSession {
		return self::$sessions[spl_object_id($player)];
	}

	public function getAll(): array {
		return self::$sessions;
	}

	public static function destroy(Player $player): void {
		unset(self::$sessions[spl_object_id($player)]);
	}

	/** @var string */
	private $uuid;
	/** @var BasePet[] */
	private $pets = [];
	/** @var string|null */
	private $riding;
	/** @var PetSelectionData|null */
	private $selection_data;

	private function __construct(Loader $loader, Player $player) {
		$this->uuid = $player->getRawUniqueId();
		$loader->getDatabase()->load($player->getName(), [$this, "onLoad"]);
	}

	/**
	 * @return Player
	 */
	public function getPlayer(): ?Player {
		return Server::getInstance()->getPlayerByRawUUID($this->uuid);
	}

	public function hasPet(string $pet_name): bool {
		return isset($this->pets[strtolower($pet_name)]);
	}

	public function getPet(string $pet_name): ?BasePet {
		return $this->pets[strtolower($pet_name)] ?? null;
	}

	/**
	 * @return BasePet[]
	 */
	public function getPets(): array {
		return $this->pets;
	}

	public function onLoad(PetOwnerData $data): void {
		$player = $this->getPlayer();
		if($player === null || !$player->isOnline()) {
			return; // player logged out while the data was being fetched.
		}

		$pos = $player->asVector3();
		$level = $player->getLevel();
		$loader = Server::getInstance()->getPluginManager()->getPlugin("BlockPets");

		foreach($data->getPets() as $pet_data) {
			$this->onPetAdd(PetFactory::create($pet_data, $level, Entity::createBaseNBT($pos, null, $player->yaw, $player->pitch)), $loader);
		}
	}

	public function addPet(PetData $data): ?BasePet {
		if($this->hasPet($data->getName())) {
			throw new \InvalidArgumentException("Tried adding a pet to a player who already owns a pet with the name " . $data->getName() . ".");
		}

		$player = $this->getPlayer();
		$pet = PetFactory::create($data, $player->getLevel(), Entity::createBaseNBT($player->asVector3(), null, $player->yaw, $player->pitch));
		if($this->onPetAdd($pet)) {
			return $pet;
		}

		return null;
	}

	private function onPetAdd(BasePet $pet, ?Loader $loader = null): bool {
		$ev = new PetSpawnEvent($loader ?? Server::getInstance()->getPluginManager()->getPlugin("BlockPets"), $pet);
		$ev->call();
		if($ev->isCancelled()) {
			$pet->setCanSavePetData(false);
			$pet->flagForDespawn();
			return false;
		}

		$pet->spawnToAll();
		$pet->setDormant(false);
		$this->pets[strtolower($pet->getPetName())] = $pet;
		return true;
	}

	public function deletePet(BasePet $pet): void {
		Server::getInstance()->getPluginManager()->getPlugin("BlockPets")->getDatabase()->unregisterPet($this->getPlayer()->getName(), $pet->getPetName());
		$this->onPetDelete($pet);
	}

	private function onPetDelete(BasePet $pet): void {
		unset($this->pets[strtolower($pet->getPetName())]);
		$pet->setCanSavePetData(false);
		$pet->flagForDespawn();
	}

	public function setRidingPet(?BasePet $pet): void {
		$this->riding = $pet !== null ? strtolower($pet->getPetName()) : null;
	}

	public function getRidingPet(): ?BasePet {
		return $this->riding !== null ? $this->pets[$this->riding] : null;
	}

	public function isRidingPet(?BasePet $pet = null): bool {
		return $this->riding !== null ? ($pet === null || $this->riding === strtolower($pet->getPetName())) : false;
	}

	public function setSelectionData(?PetSelectionData $data): void {
		$this->selection_data = $data;
	}

	public function getSelectionData(): ?PetSelectionData {
		return $this->selection_data;
	}

	public function __destruct() {
		if(!empty($this->pets)) {
			foreach($this->pets as $pet) {
				$pet->flagForDespawn();
			}
		}
	}
}