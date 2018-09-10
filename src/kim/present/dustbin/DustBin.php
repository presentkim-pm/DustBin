<?php

/*
 *
 *  ____                           _   _  ___
 * |  _ \ _ __ ___  ___  ___ _ __ | |_| |/ (_)_ __ ___
 * | |_) | '__/ _ \/ __|/ _ \ '_ \| __| ' /| | '_ ` _ \
 * |  __/| | |  __/\__ \  __/ | | | |_| . \| | | | | | |
 * |_|   |_|  \___||___/\___|_| |_|\__|_|\_\_|_| |_| |_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the MIT License. see <https://opensource.org/licenses/MIT>.
 *
 * @author  PresentKim (debe3721@gmail.com)
 * @link    https://github.com/PresentKim
 * @license https://opensource.org/licenses/MIT MIT License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 */

declare(strict_types=1);

namespace kim\present\dustbin;

use kim\present\dustbin\inventory\DustBinInventory;
use kim\present\dustbin\lang\PluginLang;
use kim\present\dustbin\task\CheckUpdateAsyncTask;
use pocketmine\command\{
	Command, CommandSender, PluginCommand
};
use pocketmine\permission\{
	Permission, PermissionManager
};
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

class DustBin extends PluginBase{
	/** @var DustBin */
	private static $instance = null;

	/**
	 * @return DustBin
	 */
	public static function getInstance() : DustBin{
		return self::$instance;
	}

	/** @var PluginLang */
	private $language;

	/** @var PluginCommand */
	private $command;

	/**
	 * Called when the plugin is loaded, before calling onEnable()
	 */
	public function onLoad() : void{
		self::$instance = $this;
	}

	/**
	 * Called when the plugin is enabled
	 */
	public function onEnable() : void{
		//Save default resources
		$this->saveResource("lang/eng/lang.ini", false);
		$this->saveResource("lang/kor/lang.ini", false);
		$this->saveResource("lang/language.list", false);

		//Load config file
		$this->saveDefaultConfig();
		$this->reloadConfig();
		$config = $this->getConfig();

		//Check latest version
		if($config->getNested("settings.update-check", false)){
			$this->getServer()->getAsyncPool()->submitTask(new CheckUpdateAsyncTask());
		}

		//Load language file
		$this->language = new PluginLang($this, $config->getNested("settings.language"));
		$this->getLogger()->info($this->language->translate("language.selected", [$this->language->getName(), $this->language->getLang()]));

		//Register main command
		$this->command = new PluginCommand($config->getNested("command.name"), $this);
		$this->command->setPermission("dustbin.cmd");
		$this->command->setAliases($config->getNested("command.aliases"));
		$this->command->setUsage($this->language->translate("commands.dustbin.usage"));
		$this->command->setDescription($this->language->translate("commands.dustbin.description"));
		$this->getServer()->getCommandMap()->register($this->getName(), $this->command);

		//Load permission's default value from config
		$permissions = PermissionManager::getInstance()->getPermissions();
		$defaultValue = $config->getNested("permission.main");
		if($defaultValue !== null){
			$permissions["dustbin.cmd"]->setDefault(Permission::getByName($config->getNested("permission.main")));
		}
	}

	/**
	 * @param CommandSender $sender
	 * @param Command       $command
	 * @param string        $label
	 * @param string[]      $args
	 *
	 * @return bool
	 */
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if($sender instanceof Player){
			$sender->addWindow(new DustBinInventory());
		}else{
			$sender->sendMessage($this->language->translate("commands.generic.onlyPlayer"));
		}
		return true;
	}

	/**
	 * @Override for multilingual support of the config file
	 *
	 * @return bool
	 */
	public function saveDefaultConfig() : bool{
		$resource = $this->getResource("lang/{$this->getServer()->getLanguage()->getLang()}/config.yml");
		if($resource === null){
			$resource = $this->getResource("lang/" . PluginLang::FALLBACK_LANGUAGE . "/config.yml");
		}

		if(!file_exists($configFile = $this->getDataFolder() . "config.yml")){
			$ret = stream_copy_to_stream($resource, $fp = fopen($configFile, "wb")) > 0;
			fclose($fp);
			fclose($resource);
			return $ret;
		}
		return false;
	}

	/**
	 * @return PluginLang
	 */
	public function getLanguage() : PluginLang{
		return $this->language;
	}
}