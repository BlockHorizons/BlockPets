<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\datastorage;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;

abstract class BaseDataStorer {

	public function __construct(protected Loader $loader) {
		$this->prepare();
		if($loader->getBlockPetsConfig()->doHardReset()) {
			$this->reset();
			$this->getLoader()->getConfig()->set("Hard-Reset", false);
		}
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
	 */
	public abstract function patch(string $version): void;

	protected function getLoader(): Loader {
		return $this->loader;
	}

	/**
	 * Resets all values in the database.
	 */
	protected abstract function reset(): void;

	/**
	 * Registers pet to the database.
	 * If the pet's entry already exists in the
	 * database, the database will perform an
	 * UPDATE-ALL-VALUES instead.
	 */
	public abstract function registerPet(BasePet $pet): void;

	/**
	 * Deletes the pet's entry from the database
	 * if exists.
	 */
	public abstract function unregisterPet(BasePet $pet): void;

	/**
	 * Updates pet's level and level points if it's
	 * entry exists in the database.
	 */
	public abstract function updateExperience(BasePet $pet): void;

	/**
	 * Retrieves all of the owner's pets from the
	 * database and then calls the callable to
	 * initialize the fetched entries.
	 */
	public abstract function load(string $ownerName, callable $callable): void;

	/**
	 * Fetches all pets' names of the specified player
	 * from the database and calls the callable to get
	 * the list of pet names.
	 * If $entityName is not null, only entities with the
	 * specified entity name will be fetched.
	 */
	public abstract function getPlayerPets(string $ownerName, ?string $entityName = null, callable $callable = null): void;

	/**
	 * Fetches all pets sorted by their level and points
	 * and calls the callable to get the list of sorted
	 * pets.
	 * If $entityName is not null, only entities with the
	 * specified entity name will be fetched.
	 */
	public abstract function getPetsLeaderboard(int $offset = 0, int $length = 1, ?string $entityName = null, callable $callable = null): void;

	/**
	 * Toggles pets on or off from the database.
	 */
	public abstract function togglePets(string $owner, ?string $petName, callable $callable): void;

	/**
	 * Updates the database with whether the pet is
	 * chested or not.
	 */
	public abstract function updateChested(BasePet $pet): void;

	/**
	 * Updates the database with the pet's inventory
	 * contents.
	 */
	public abstract function updateInventory(BasePet $pet): void;

	/**
	 * Called during plugin disable to let databases
	 * close their instances.
	 */
	protected abstract function close(): void;
}