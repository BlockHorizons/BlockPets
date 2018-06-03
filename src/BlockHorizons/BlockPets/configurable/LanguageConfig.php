<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\configurable;

use BlockHorizons\BlockPets\Loader;
use pocketmine\utils\TextFormat;

class LanguageConfig {

	private $loader;
	private $messages = [];

	public function __construct(Loader $loader) {
		$this->loader = $loader;

		$this->collectMessages();
	}

	public function collectMessages(): void {
		$languageSelected = false;
		$language = [];
		foreach($this->getLoader()->getAvailableLanguages() as $availableLanguage) {
			if($this->getLoader()->getBlockPetsConfig()->getLanguage() === $availableLanguage) {
				$this->getLoader()->saveResource("languages/" . $availableLanguage . ".yml", true);
				$language = yaml_parse_file($this->getLoader()->getDataFolder() . "languages/" . $availableLanguage . ".yml");
				$languageSelected = true;
				break;
			}
		}
		if(!$languageSelected) {
			$this->getLoader()->saveResource("languages/en.yml");
			$language = yaml_parse_file($this->getLoader()->getDataFolder() . "languages/en.yml");
		}

		$iterator = new \RecursiveTreeIterator(new \RecursiveArrayIterator($language));
		$keys = [];
		$current_index = NAN;

		foreach($iterator as $value) {
			$prefix = str_replace("\\", "|", $iterator->getPrefix());

			$index = strpos($prefix, "|-");
			if($index !== false) {
				$index = max(0, $index / 2);
			}

			if(count($keys) > $index) {
				$keys = array_slice($keys, 0, $index + 1);
			}

			$keys[$index] = $iterator->key();
			$entry = $iterator->getEntry();

			if($entry !== "Array") {
				$this->messages[implode(".", $keys)] = TextFormat::colorize($entry);
			}
		}
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}

	/**
	 * @param string $key
	 *
	 * @return string
	 */
	public function get(string $key): string {
		return $this->messages[$key] ?? $key;
	}
}
