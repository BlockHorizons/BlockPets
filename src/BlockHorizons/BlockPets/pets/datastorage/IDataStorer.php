<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\datastorage;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\datastorage\types\PetData;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use pocketmine\utils\UUID;

interface IDataStorer {

	/**
	 * Called during class construction to let
	 * databases create and initialize their
	 * instances.
	 *
	 * @param Loader $loader
	 */
	public function prepare(Loader $loader): void;

	/**
	 * Called when the plugin updates so database
	 * can perform patches (if any).
	 *
	 * @param string $version
	 */
	public function patch(string $version): void;

	/**
	 * Inserts pet data into the database. Do NOT use
	 * this method as a means to mass-update pet data.
	 *
	 * @param PetData $data
	 */
	public function createPet(PetData $data): void;

	/**
	 * Deletes a pet's entry from the database.
	 *
	 * @param UUID $uuid
	 */
	public function deletePet(UUID $uuid): void;

	/**
	 * Fetches all pets assosciated with the given player.
	 *
	 * @param Player $ownerName
	 * @param callable $on_load_player
	 */
	public function loadPlayer(Player $player, callable $on_load_player): void;

	/**
	 * Fetches all pets sorted by their level and points
	 * and calls the callable to get the list of sorted
	 * pets. If $type is not null, only entities of the
	 * specified type will be fetched.
	 *
	 * @param int $offset
	 * @param int $length
	 * @param string|null $type
	 * @param callable $callable
	 */
	public function getPetsLeaderboard(int $offset = 0, int $length = 1, ?string $type = null, callable $callable): void;

	/**
	 * Updates the pet's name in the database.
	 *
	 * @param UUID $uuid
	 * @param string $new_name
	 */
	public function updatePetName(UUID $uuid, string $new_name): void;

	/**
	 * Updates the pet's points in the database.
	 *
	 * @param UUID $uuid
	 * @param int $points
	 */
	public function updatePetPoints(UUID $uuid, int $points): void;

	/**
	 * Updates the pet's nbt data in the database.
	 *
	 * @param UUID $uuid
	 * @param CompoundTag $nbt
	 */
	public function updatePetNBT(UUID $uuid, CompoundTag $nbt): void;

	/**
	 * Called during plugin disable to let databases
	 * close their instances.
	 */
	public function close(): void;
}