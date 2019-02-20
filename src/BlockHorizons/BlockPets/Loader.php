<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets;

use BlockHorizons\BlockPets\commands\CommandFactory;
use BlockHorizons\BlockPets\configurable\BlockPetsConfig;
use BlockHorizons\BlockPets\configurable\LanguageConfig;
use BlockHorizons\BlockPets\configurable\PetProperties;
use BlockHorizons\BlockPets\items\Saddle;
use BlockHorizons\BlockPets\listeners\EventListener;
use BlockHorizons\BlockPets\listeners\RidingListener;
use BlockHorizons\BlockPets\pets\BasePet;
use BlockHorizons\BlockPets\pets\PetFactory;
use BlockHorizons\BlockPets\pets\datastorage\BaseDataStorer;
use BlockHorizons\BlockPets\pets\datastorage\MySQLDataStorer;
use BlockHorizons\BlockPets\pets\datastorage\SQLiteDataStorer;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\lang\BaseLang;
use spoondetector\SpoonDetector;

class Loader extends PluginBase {

	private $availableLanguages = [
		"en",
		"nl",
		"vi",
		"gr",
		"ko",
		"de"
	];

	/** @var BlockPetsConfig */
	private $bpConfig;
	/** @var PetProperties */
	private $pProperties;
	/** @var LanguageConfig */
	private $language;

	/** @var BaseDataStorer */
	private $database;

	public function onEnable() {
		SpoonDetector::printSpoon($this);

		CommandFactory::init($this);
		PetFactory::init($this);
		$this->registerItems();
		$this->registerListeners();

		$this->bpConfig = new BlockPetsConfig($this);
		$this->pProperties = new PetProperties($this);
		$this->language = new LanguageConfig($this);
		$this->registerDatabase();

		$this->checkVersionChange();
	}

	private function checkVersionChange(): void {
		$this->saveResource(".version_file");
		$version_file = $this->getDataFolder() . ".version_file";
		$current_version = yaml_parse_file($version_file)["version"];

		if(version_compare($this->getDescription()->getVersion(), $current_version, '>')) {
			$this->updateVersion($current_version);
		}
	}

	private function updateVersion(string $current_version): void {
		$current = (int) str_replace(".", "", $current_version);
		$newest = (int) str_replace(".", "", $this->getDescription()->getVersion());
		while($current < $newest) {
			++$current;
			$version = implode(".", str_split((string) $current));
			$this->onVersionUpdate($version);
		}

		$this->saveResource(".version_file", true);
	}

	private function onVersionUpdate(string $version): void {
		$this->getDatabase()->patch($version);
	}

	public function registerItems(): void {
		ItemFactory::registerItem(new Saddle(), true);
		Item::addCreativeItem(Item::get(Item::SADDLE));
	}

	public function registerListeners(): void {
		$listeners = [
			new EventListener($this),
			new RidingListener($this)
		];
		foreach($listeners as $listener) {
			$this->getServer()->getPluginManager()->registerEvents($listener, $this);
		}
	}

	/**
	 * @return string[]
	 */
	public function getAvailableLanguages(): array {
		return $this->availableLanguages;
	}

	private function registerDatabase(): void {
		$database_stmts = $this->getDataFolder() . "database_stmts/";
		if(!is_dir($database_stmts)) {
			mkdir($database_stmts);
		}

		$this->saveResource("database_stmts/mysql.sql", true);
		$this->saveResource("database_stmts/sqlite.sql", true);

		switch(strtolower($this->getBlockPetsConfig()->getDatabase())) {
			default:
			case "mysql":
				$this->database = new MySQLDataStorer($this);
				break;
			case "sqlite":
			case "sqlite3":
				$this->database = new SQLiteDataStorer($this);
				break;
		}
	}

	/**
	 * @return BlockPetsConfig
	 */
	public function getBlockPetsConfig(): BlockPetsConfig {
		return $this->bpConfig;
	}

	/**
	 * @param string $key
	 * @param array  $params
	 *
	 * @return string
	 */
	public function translate(string $key, array $params = []): string {
		if(!empty($params)) {
			return vsprintf($this->getLanguage()->get($key), $params);
		}
		return $this->getLanguage()->get($key);
	}

	/**
	 * @return LanguageConfig
	 */
	public function getLanguage(): LanguageConfig {
		return $this->language;
	}

	/**
	 * Returns the database to store and fetch data from.
	 *
	 * @return BaseDataStorer
	 */
	public function getDatabase(): BaseDataStorer {
		if($this->database === null) {
			throw new \RuntimeException("Attempted to retrieve the database while database storing was unavailable.");
		}
		return $this->database;
	}

	/**
	 * @return PetProperties
	 */
	public function getPetProperties(): PetProperties {
		return $this->pProperties;
	}
}
