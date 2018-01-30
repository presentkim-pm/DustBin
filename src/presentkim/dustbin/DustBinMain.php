<?php

namespace presentkim\dustbin;

use pocketmine\command\PluginCommand;
use pocketmine\plugin\PluginBase;
use presentkim\dustbin\command\CommandListener;
use presentkim\dustbin\util\Translation;

class DustBinMain extends PluginBase{

    /** @var self */
    private static $instance = null;

    /** @return self */
    public static function getInstance() : self{
        return self::$instance;
    }

    /** @var PluginCommand */
    private $command = null;

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

        if ($this->command !== null) {
            $this->getServer()->getCommandMap()->unregister($this->command);
        }

        $this->command = new PluginCommand(Translation::translate('command-dustbin'), $this);
        $this->command->setExecutor(new CommandListener($this));
        $this->command->setPermission('dustbin.cmd');
        $this->command->setDescription(Translation::translate('command-dustbin@description'));
        $this->command->setUsage(Translation::translate('command-dustbin@usage'));
        if (is_array($aliases = Translation::getArray('command-dustbin@aliases'))) {
            $this->command->setAliases($aliases);
        }
        $this->getServer()->getCommandMap()->register('dustbin', $this->command);
    }
}
