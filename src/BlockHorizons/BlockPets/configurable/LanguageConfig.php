<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\configurable;

use BlockHorizons\BlockPets\Loader;

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
				$this->getLoader()->saveResource("languages/" . $availableLanguage . ".yml");
				$language = yaml_parse_file($this->getLoader()->getDataFolder() . "languages/" . $availableLanguage . ".yml");
				$languageSelected = true;
				break;
			}
		}
		if(!$languageSelected) {
			$this->getLoader()->saveResource("languages/en.yml");
			$language = yaml_parse_file($this->getLoader()->getDataFolder() . "languages/en.yml");
		}

		// Don't look at this code, it may harm you — or start a nuclear war. BlockHorizons and it's developers are not responsible for any physical damage received as a result of staring at this code. You have been warned.
		$this->messages = [
			"prefix.warning" => $language["prefix"]["warning"],

			"commands.errors.console-use" => $language["commands"]["errors"]["console-use"],
			"commands.errors.no-permission" => $language["commands"]["errors"]["no-permission"],
			"commands.errors.plugin-cancelled" => $language["commands"]["errors"]["plugin-cancelled"],

			"commands.errors.pet.doesnt-exist" => $language["commands"]["errors"]["pet"]["doesnt-exist"],
			"commands.errors.pet.numeric" => $language["commands"]["errors"]["pet"]["numeric"],
			"commands.errors.player.not-found" => $language["commands"]["errors"]["player"]["not-found"],
			"commands.errors.player.no-pet" => $language["commands"]["errors"]["player"]["no-pet"],
			"commands.errors.player.no-pet-other" => $language["commands"]["errors"]["player"]["no-pet-other"],
			"commands.errors.player.already-own-pet" => $language["commands"]["errors"]["player"]["already-own-pet"],

			"commands.changepetname.no-permission" => $language["commands"]["changepetname"]["no-permission"],
			"commands.changepetname.success" => $language["commands"]["changepetname"]["success"],

			"commands.healpet.success" => $language["commands"]["healpet"]["success"],

			"commands.leveluppet.success" => $language["commands"]["leveluppet"]["success"],

			"commands.spawnpet.no-permission" => $language["commands"]["spawnpet"]["no-permission"],
			"commands.spawnpet.no-permission.other" => $language["commands"]["spawnpet"]["no-permission-others"],
			"commands.spawnpet.success" => $language["commands"]["spawnpet"]["success"],
			"commands.spawnpet.success.other" => $language["commands"]["spawnpet"]["success-other"],
			"commands.spawnpet.name" => $language["commands"]["spawnpet"]["name"],
			"commands.spawnpet.selecting-name" => $language["commands"]["spawnpet"]["selecting-name"],
			"commands.spawnpet.exceeded-limit" => $language["commands"]["spawnpet"]["exceeded-limit"],

			"commands.removepet.success" => $language["commands"]["removepet"]["success"],

			"commands.togglepet.success" => $language["commands"]["togglepet"]["success"],
			"commands.togglepet.success.other" => $language["commands"]["togglepet"]["success-others"],
			"commands.togglepet.no-pet-specified" => $language["commands"]["togglepet"]["no-pet-specified"]
		];
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
		return $this->messages[$key] ?? "";
	}
}
