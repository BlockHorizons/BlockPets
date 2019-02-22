<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\datastorage;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\datastorage\types\PetData;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use pocketmine\utils\UUID;

abstract class BaseDataStorer {

	/** @var Loader */
	protected $loader;

	public function __construct(Loader $loader) {
		$this->loader = $loader;
		$this->prepare();
	}

	/**
	 * @return Loader
	 */
	protected function getLoader(): Loader {
		return $this->loader;
	}

	/**
	 * Called during class construction to let
	 * databases create and initialize their
	 * instances.
	 */
	protected abstract function prepare(): void;

	/**
	 * Called when the plugin updates so database
	 * can perform patches (if any).
	 *
	 * @param string $version
	 */
	protected abstract function patch(string $version): void;

	/**
	 * Inserts pet data into the database. Do NOT use
	 * this method as a means to mass-update pet data.
	 *
	 * @param PetData $data
	 */
	public abstract function createPet(PetData $data): void;

	/**
	 * Deletes a pet's entry from the database.
	 *
	 * @param UUID $uuid
	 */
	public abstract function deletePet(UUID $uuid): void;

	/**
	 * Fetches all pets assosciated with the given player.
	 *
	 * @param Player $ownerName
	 * @param callable $on_load_player
	 */
	public abstract function loadPlayer(Player $player, callable $on_load_player): void;

	/**
	 * Fetches all pets sorted by their level and points
	 * and calls the callable to get the list of sorted
	 * pets.
	 * If $type is not null, only entities with the
	 * specified entity name will be fetched.
	 *
	 * @param int $offset
	 * @param int $length
	 * @param string|null $type
	 * @param callable $callable
	 */
	public abstract function getPetsLeaderboard(int $offset = 0, int $length = 1, ?string $type = null, callable $callable): void;

	/**
	 * Updates the pet's name in the database.
	 *
	 * @param UUID $uuid
	 * @param string $new_name
	 */
	public abstract function updatePetName(UUID $uuid, string $new_name): void;

	/**
	 * Updates the pet's experience in the database.
	 *
	 * @param UUID $uuid
	 * @param int $xp
	 */
	public abstract function updatePetXp(UUID $uuid, int $xp): void;

	/**
	 * Updates the pet's nbt data in the database.
	 *
	 * @param UUID $uuid
	 * @param CompoundTag $nbt
	 */
	public abstract function updatePetNBT(UUID $uuid, CompoundTag $nbt): void;

	/**
	 * Called during plugin disable to let databases
	 * close their instances.
	 */
	protected abstract function close(): void;
}