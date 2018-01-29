<?php

namespace presentkim\dustbin\inventory;

use pocketmine\block\Block;
use pocketmine\Player;
use pocketmine\inventory\{
  BaseInventory, CustomInventory
};
use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\nbt\tag\{
  CompoundTag, IntTag, StringTag
};
use pocketmine\network\mcpe\protocol\{
  UpdateBlockPacket, BlockEntityDataPacket, ContainerOpenPacket
};
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\tile\Spawnable;

use presentkim\dustbin\util\Translation;

class DustBinInventory extends CustomInventory{

    /** @var NetworkLittleEndianNBTStream|null */
    private static $nbtWriter = null;

    /** Vector3 */
    private $vec;

    /**
     * @param Player $who
     */
    public function onOpen(Player $who) : void{
        BaseInventory::onOpen($who);

        $this->vec = $who->floor()->add(0, 5, 0);

        if (self::$nbtWriter === null) {
            self::$nbtWriter = new NetworkLittleEndianNBTStream();
        }
        self::$nbtWriter->setData(new CompoundTag("", [
          new StringTag("id", "Chest"),
          new IntTag("x", $this->vec->x),
          new IntTag("y", $this->vec->y),
          new IntTag("z", $this->vec->z),
          new StringTag("CustomName", Translation::translate('dustbin-name')),
        ]));

        $pk = new UpdateBlockPacket();
        $pk->blockId = Block::CHEST;
        $pk->blockData = 0;
        $pk->x = $this->vec->x;
        $pk->y = $this->vec->y;
        $pk->z = $this->vec->z;
        $who->sendDataPacket($pk);

        $pk = new BlockEntityDataPacket();
        $pk->x = $this->vec->x;
        $pk->y = $this->vec->y;
        $pk->z = $this->vec->z;
        $pk->namedtag = self::$nbtWriter->write();
        $who->sendDataPacket($pk);


        $pk = new ContainerOpenPacket();
        $pk->type = WindowTypes::CONTAINER;
        $pk->entityUniqueId = -1;
        $pk->x = $this->vec->x;
        $pk->y = $this->vec->y;
        $pk->z = $this->vec->z;
        $pk->windowId = $who->getWindowId($this);
        $who->sendDataPacket($pk);

        $this->sendContents($who);
    }

    public function onClose(Player $who) : void{
        BaseInventory::onClose($who);

        $block = $who->getLevel()->getBlock($this->vec);

        $pk = new UpdateBlockPacket();
        $pk->x = $this->vec->x;
        $pk->y = $this->vec->y;
        $pk->z = $this->vec->z;
        $pk->blockId = $block->getId();
        $pk->blockData = $block->getDamage();
        $who->sendDataPacket($pk);

        $tile = $who->getLevel()->getTile($this->vec);
        if ($tile instanceof Spawnable) {
            $who->sendDataPacket($tile->createSpawnPacket());
        }
        $this->clearAll();
    }

    /** @return string */
    public function getName() : string{
        return "DustBin";
    }

    /** @return int */
    public function getDefaultSize() : int{
        return 27;
    }

    /** @return int */
    public function getNetworkType() : int{
        return WindowTypes::CONTAINER;
    }
}