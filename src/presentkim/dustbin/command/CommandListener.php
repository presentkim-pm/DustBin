<?php

namespace presentkim\dustbin\command\subcommands;

namespace presentkim\dustbin\command;

use pocketmine\command\{
  Command, CommandExecutor, CommandSender
};
use presentkim\dustbin\DustBinMain as Plugin;
use presentkim\dustbin\util\Translation;

class CommandListener implements CommandExecutor{

    /** @var Plugin */
    protected $owner;

    /** @param Plugin $owner */
    public function __construct(Plugin $owner){
        $this->owner = $owner;
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

        return true;
    }
}