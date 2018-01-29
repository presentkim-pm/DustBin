<?php

namespace presentkim\dustbin;

use pocketmine\Player;
use pocketmine\command\{
  CommandExecutor, PluginCommand
};
use pocketmine\plugin\PluginBase;

use presentkim\dustbin\command\CommandListener;
use presentkim\dustbin\inventory\DustBinInventory;
use presentkim\dustbin\util\Translation;

class DustBinMain extends PluginBase{

    /** @var self */
    private static $instance = null;

    /** @var PluginCommand[] */
    private $commands = [];

    /** @var DustBinInventory[] DustBinInventory[string] */
    private $bins = [];

    /** @return self */
    public static function getInstance() : self{
        return self::$instance;
    }

    public function onLoad() : void{
        if (self::$instance === null) {
            self::$instance = $this;
        }
    }

    public function onEnable() : void{
        $this->load();
    }

    public function load() : void{
        $dataFolder = $this->getDataFolder();
        if (!file_exists($dataFolder)) {
            mkdir($dataFolder, 0777, true);
        }

        $langfilename = $dataFolder . 'lang.yml';
        if (!file_exists($langfilename)) {
            $resource = $this->getResource('lang/eng.yml');
            fwrite($fp = fopen("{$dataFolder}lang.yml", "wb"), $contents = stream_get_contents($resource));
            fclose($fp);
            Translation::loadFromContents($contents);
        } else {
            Translation::load($langfilename);
        }

        foreach ($this->commands as $command) {
            $this->getServer()->getCommandMap()->unregister($command);
        }
        $this->commands = [];

        // register commands
        $this->registerCommand(new CommandListener($this), Translation::translate('command-dustbin'), 'DustBin', 'dustbin.cmd', Translation::translate('command-dustbin@description'), Translation::translate('command-dustbin@usage'), Translation::getArray('command-dustbin@aliases'));
    }

    /**
     * @param CommandExecutor $executor
     * @param                 $name
     * @param                 $fallback
     * @param                 $permission
     * @param string          $description
     * @param null            $usageMessage
     * @param array|null      $aliases
     */
    private function registerCommand(CommandExecutor $executor, $name, $fallback, $permission, $description = "", $usageMessage = null, array $aliases = null) : void{
        $command = new PluginCommand($name, $this);
        $command->setExecutor($executor);
        $command->setPermission($permission);
        $command->setDescription($description);
        $command->setUsage($usageMessage ?? ('/' . $name));
        if (is_array($aliases)) {
            $command->setAliases($aliases);
        }

        $this->getServer()->getCommandMap()->register($fallback, $command);
        $this->commands[] = $command;
    }

    /**
     * @param Player $player
     *
     * @return DustBinInventory
     */
    public function getDustbin(Player $player) : DustBinInventory{
        $playerName = $player->getName();
        if (!isset($this->bins[$playerName])) {
            $this->bins[$playerName] = new DustBinInventory($player);
        }

        return $this->bins[$playerName];
    }
}
