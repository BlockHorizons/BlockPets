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
use pocketmine\utils\UUID;

class PlayerSession {

	/** @var PlayerSession[] */
	private static $sessions = [];

	/**
	 * Creates a session for a player. This is already
	 * done when the player joins the server.
	 *
	 * @param Loader $loader
	 * @param Player $player
	 */
	public static function create(Loader $loader, Player $player): void {
		if(isset(self::$sessions[$id = spl_object_id($player)])) {
			throw new \RuntimeError("Attempted to create PlayerSession for a player already having a PlayerSession.");
		}

		self::$sessions[$id] = new PlayerSession($loader, $player);
	}

	/**
	 * Gets a player's session.
	 * WARNING: Calling this method before PlayerJoinEvent
	 * will cause an error.
	 *
	 * @param Player $player
	 *
	 * @return PlayerSession.
	 */
	public static function get(Player $player): PlayerSession {
		return self::$sessions[spl_object_id($player)];
	}

	/**
	 * Gets all player sessions.
	 *
	 * @return PlayerSession[]
	 */
	public static function getAll(): array {
		return self::$sessions;
	}

	/**
	 * Destroys a player's session. This is already
	 * done when the player quits the server.
	 *
	 * @param Player $player
	 */
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
		$loader->getDatabase()->loadPlayer($player, [$this, "onLoad"]);
	}

	/**
	 * Returns the player owning this session.
	 *
	 * @return Player
	 */
	public function getPlayer(): ?Player {
		return Server::getInstance()->getPlayerByRawUUID($this->uuid);
	}

	protected function getLoader(): Loader {
		return Server::getInstance()->getPluginManager()->getPlugin("BlockPets");
	}

	public function ownsPet(BasePet $pet): bool {
		return $this->getPet($pet->getPetUUID()) !== null;
	}

	public function getPet(UUID $uuid): ?BasePet {
		return $this->getPetByRawUUID($uuid->toBinary());
	}

	/**
	 * Returns a pet of a given UUID that this player owns
	 * or null if they don't own a pet by such a UUID.
	 *
	 * @param string $uuid
	 *
	 * @return BasePet|null
	 */
	public function getPetByRawUUID(string $uuid): ?BasePet {
		return $this->pets[$uuid] ?? null;
	}

	public function getPetByName(string $pet_name): ?BasePet {
		$pet_name = strtolower($pet_name);
		foreach($this->pets as $pet) {
			if(strtolower($pet->getPetName()) === $pet_name) {
				return $pet;
			}
		}

		return null;
	}

	/**
	 * Gets all pets of this player.
	 *
	 * @return BasePet[]
	 */
	public function getPets(): array {
		return $this->pets;
	}

	/**
	 * Called when the player's data is fetched from
	 * the database.
	 *
	 * @param PetOwnerData $data
	 */
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

	/**
	 * Adds a new pet to this player and returns the
	 * pet instance if the pet was successfully
	 * added.
	 *
	 * @param PetOwnerData $data
	 *
	 * @return BasePet|null
	 */
	public function addPet(PetData $data): ?BasePet {
		if($this->getPet($data->getUUID()) !== null) {
			throw new \InvalidArgumentException("Tried adding a pet to a player who already owns a pet with the name " . $data->getName() . ".");
		}

		$loader = $this->getLoader();
		$loader->getDatabase()->createPet($data);

		$player = $this->getPlayer();
		$pet = PetFactory::create($data, $player->getLevel(), Entity::createBaseNBT($player->asVector3(), null, $player->yaw, $player->pitch));
		if($this->onPetAdd($pet, $loader)) {
			return $pet;
		}

		return null;
	}

	private function onPetAdd(BasePet $pet, ?Loader $loader = null): bool {
		$ev = new PetSpawnEvent($loader ?? $this->getLoader(), $pet);
		$ev->call();
		if($ev->isCancelled()) {
			$pet->setCanSavePetData(false);
			$pet->flagForDespawn();
			return false;
		}

		$pet->spawnToAll();
		$pet->setDormant(false);
		$this->pets[$pet->getPetUUID()->toBinary()] = $pet;
		return true;
	}

	/**
	 * Deletes a pet that this player owns.
	 *
	 * @param BasePet $pet
	 */
	public function deletePet(BasePet $pet): void {
		if(!$this->ownsPet($pet)) {
			throw new \InvalidArgumentException("Tried deleting a pet from a player that doesn't own the pet.");
		}

		Server::getInstance()->getPluginManager()->getPlugin("BlockPets")->getDatabase()->deletePet($pet->getPetUUID());
		$this->onPetDelete($pet);
	}

	private function onPetDelete(BasePet $pet): void {
		unset($this->pets[$pet->getPetUUID()->toBinary()]);
		$pet->setCanSavePetData(false);
		$pet->flagForDespawn();
	}

	/**
	 * Sets the pet this player is riding.
	 *
	 * @param BasePet|null $pet
	 */
	public function setRidingPet(?BasePet $pet): void {
		$this->riding = $pet !== null ? $pet->getPetUUID()->toBinary() : null;
	}

	/**
	 * Returns the pet this player is riding.
	 *
	 * @return BasePet|null
	 */
	public function getRidingPet(): ?BasePet {
		return $this->riding !== null ? $this->pets[$this->riding] : null;
	}

	/**
	 * Returns whether this player is riding
	 * a pet.
	 *
	 * @param BasePet|null if specified, returns
	 * whether this player is riding the specified
	 * pet. Or else returns whether this player is
	 * riding any pet.
	 *
	 * @return bool
	 */
	public function isRidingPet(?BasePet $pet = null): bool {
		return $this->riding !== null && ($pet === null || $this->riding === $pet->getPetUUID()->toBinary());
	}

	/**
	 * Sets this player's selection data. This is
	 * a cache object queued until the next PlayerChatEvent
	 * and carries the pet data required to create a pet.
	 *
	 * @pararm PetSelectionData|null $data
	 */
	public function setSelectionData(?PetSelectionData $data): void {
		$this->selection_data = $data;
	}

	/**
	 * @return PetSelectionData|null
	 */
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