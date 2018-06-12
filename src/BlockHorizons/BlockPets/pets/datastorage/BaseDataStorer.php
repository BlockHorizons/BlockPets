<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\datastorage;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;

abstract class BaseDataStorer {

	/** @var Loader */
	protected $loader;

	public function __construct(Loader $loader) {
		$this->loader = $loader;
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
	 *
	 * @param string $version
	 */
	protected abstract function patch(string $version): void;

	/**
	 * @return Loader
	 */
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
	 *
	 * @param BasePet $pet
	 */
	public abstract function registerPet(BasePet $pet): void;

	/**
	 * Deletes the pet's entry from the database
	 * if exists.
	 *
	 * @param BasePet $pet
	 */
	public abstract function unregisterPet(BasePet $pet): void;

	/**
	 * Updates pet's level and level points if it's
	 * entry exists in the database.
	 *
	 * @param BasePet $pet
	 */
	public abstract function updateExperience(BasePet $pet): void;

	/**
	 * Retrieves all of the owner's pets from the
	 * database and then calls the callable to
	 * initialize the fetched entries.
	 *
	 * @param string $ownerName
	 * @param callable $callable
	 */
	public abstract function load(string $ownerName, callable $callable): void;

	/**
	 * Fetches all pets' names of the specified player
	 * from the database and calls the callable to get
	 * the list of pet names.
	 * If $entityName is not null, only entities with the
	 * specified entity name will be fetched.
	 *
	 * @param string $ownerName
	 * @param string|null $entityName
	 * @param callable $callable
	 */
	public abstract function getPlayerPets(string $ownerName, ?string $entityName = null, callable $callable): void;

	/**
	 * Fetches all pets sorted by their level and points
	 * and calls the callable to get the list of sorted
	 * pets.
	 * If $entityName is not null, only entities with the
	 * specified entity name will be fetched.
	 *
	 * @param int $offset
	 * @param int $length
	 * @param string|null $entityName
	 * @param callable $callable
	 */
	public abstract function getPetsLeaderboard(int $offset = 0, int $length = 1, ?string $entityName = null, callable $callable): void;

	/**
	 * Toggles pets on or off from the database.
	 *
	 * @param string $ownerName
	 * @param string|null $petName
	 */
	public abstract function togglePets(string $owner, ?string $petName, callable $callable): void;

	/**
	 * Updates the database with whether the pet is
	 * chested or not.
	 *
	 * @param BasePet $pet
	 */
	public abstract function updateChested(BasePet $pet): void;

	/**
	 * Updates the database with the pet's inventory
	 * contents.
	 *
	 * @param BasePet $pet
	 */
	public abstract function updateInventory(BasePet $pet): void;

	/**
	 * Called during plugin disable to let databases
	 * close their instances.
	 */
	protected abstract function close(): void;
}