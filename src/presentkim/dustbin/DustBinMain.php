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

    /** @return self */
    public static function getInstance() : self{
        return self::$instance;
    }

    /** @var PluginCommand[] */
    private $commands = [];

    /** @var DustBinInventory[] DustBinInventory[string] */
    private $bins = [];

    public function onLoad() : void{
        if (self::$instance === null) {
            self::$instance = $this;
        }
    }

    public function onEnable() : void{
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

        $command = new PluginCommand(Translation::translate('command-dustbin'), $this);
        $command->setExecutor(new CommandListener($this));
        $command->setPermission('dustbin.cmd');
        $command->setDescription(Translation::translate('command-dustbin@description'));
        $command->setUsage(Translation::translate('command-dustbin@usage'));
        if (is_array($aliases = Translation::getArray('command-dustbin@aliases'))) {
            $command->setAliases($aliases);
        }
        $this->getServer()->getCommandMap()->register('dustbin', $command);
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
