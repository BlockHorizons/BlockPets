<?php

namespace BlockHorizons\BlockPets\pets\datastorage;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;

abstract class BaseDataStorer {

	protected $loader;

	public function __construct(Loader $loader) {
		$this->loader = $loader;
		$this->prepare();

		if($this->getLoader()->getBlockPetsConfig()->doHardReset()) {
			$this->reset();
			$this->getLoader()->getConfig()->set("Hard-Reset", false);
		}
	}

	/**
	 * @return bool
	 */
	protected abstract function prepare(): bool;

	/**
	 * @return Loader
	 */
	protected function getLoader(): Loader {
		return $this->loader;
	}

	/**
	 * @return bool
	 */
	protected abstract function reset(): bool;

	/**
	 * @param BasePet $pet
	 *
	 * @return bool
	 */
	public abstract function registerPet(BasePet $pet): bool;

	/**
	 * @param string $petName
	 * @param string $ownerName
	 *
	 * @return bool
	 */
	public abstract function petExists(string $petName, string $ownerName): bool;

	/**
	 * @param string $petName
	 * @param string $ownerName
	 *
	 * @return bool
	 */
	public abstract function unregisterPet(string $petName, string $ownerName): bool;

	/**
	 * @param string $petName
	 * @param string $ownerName
	 * @param int    $petLevel
	 * @param int    $levelPoints
	 *
	 * @return bool
	 */
	public abstract function updatePetExperience(string $petName, string $ownerName, int $petLevel, int $levelPoints): bool;

	/**
	 * @param string $ownerName
	 *
	 * @return array
	 */
	public abstract function fetchAllPetData(string $ownerName): array;

	/**
	 * @param string $petName
	 * @param string $ownerName
	 *
	 * @return bool
	 */
	public abstract function updateChested(string $petName, string $ownerName): bool;

	/**
	 * @param string $petName
	 * @param string $ownerName
	 * @param string $contents
	 *
	 * @return bool
	 */
	public abstract function updateInventory(string $petName, string $ownerName, string $contents): bool;

	/**
	 * @param string $petName
	 * @param string $ownerName
	 *
	 * @return string
	 */
	public abstract function getInventory(string $petName, string $ownerName): array;

	/**
	 * @return bool
	 */
	protected abstract function close(): bool;
}