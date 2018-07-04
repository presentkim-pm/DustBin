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
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author  PresentKim (debe3721@gmail.com)
 * @link    https://github.com/PresentKim
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0.0
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 */

declare(strict_types=1);

namespace kim\present\dustbin\inventory;

use kim\present\dustbin\DustBin;
use pocketmine\block\{
	Block, BlockFactory
};
use pocketmine\inventory\{
	BaseInventory, CustomInventory
};
use pocketmine\math\Vector3;
use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\nbt\tag\{
	CompoundTag, StringTag
};
use pocketmine\network\mcpe\protocol\{
	BlockEntityDataPacket, ContainerOpenPacket, UpdateBlockPacket
};
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;
use pocketmine\tile\Spawnable;

class DustBinInventory extends CustomInventory{
	/** @var CompoundTag */
	private $nbt;

	public function __construct(){
		parent::__construct(new Vector3(), [], $this->getDefaultSize(), null);

		$this->nbt = new CompoundTag("", [
			new StringTag("id", "Chest"),
			new StringTag("CustomName", DustBin::getInstance()->getLanguage()->translateString("dustbin.name")),
		]);
	}

	/**
	 * @param Player $who
	 */
	public function onOpen(Player $who) : void{
		BaseInventory::onOpen($who);

		$this->holder = $who->subtract(0, 3, 0)->floor();
		if($this->holder->y < 0){
			$this->holder->y = 0;
		}

		$pk = new UpdateBlockPacket();
		$pk->blockRuntimeId = BlockFactory::toStaticRuntimeId(Block::CHEST);
		$pk->x = $this->holder->x;
		$pk->y = $this->holder->y;
		$pk->z = $this->holder->z;
		$pk->flags = UpdateBlockPacket::FLAG_NONE;
		$who->sendDataPacket($pk);


		$this->nbt->setInt("x", $this->holder->x);
		$this->nbt->setInt("y", $this->holder->y);
		$this->nbt->setInt("z", $this->holder->z);

		$pk = new BlockEntityDataPacket();
		$pk->x = $this->holder->x;
		$pk->y = $this->holder->y;
		$pk->z = $this->holder->z;
		$pk->namedtag = (new NetworkLittleEndianNBTStream())->write($this->nbt);
		$who->sendDataPacket($pk);


		$pk = new ContainerOpenPacket();
		$pk->type = WindowTypes::CONTAINER;
		$pk->entityUniqueId = -1;
		$pk->x = $this->holder->x;
		$pk->y = $this->holder->y;
		$pk->z = $this->holder->z;
		$pk->windowId = $who->getWindowId($this);
		$who->sendDataPacket($pk);

		$this->sendContents($who);
	}

	/**
	 * @param Player $who
	 */
	public function onClose(Player $who) : void{
		BaseInventory::onClose($who);

		$block = $who->getLevel()->getBlock($this->holder);

		$pk = new UpdateBlockPacket();
		$pk->x = $this->holder->x;
		$pk->y = $this->holder->y;
		$pk->z = $this->holder->z;
		$pk->blockRuntimeId = BlockFactory::toStaticRuntimeId($block->getId(), $block->getDamage());
		$pk->flags = UpdateBlockPacket::FLAG_NONE;
		$who->sendDataPacket($pk);

		$tile = $who->getLevel()->getTile($this->holder);
		if($tile instanceof Spawnable){
			$who->sendDataPacket($tile->createSpawnPacket());
		}
		$this->clearAll();
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return "DustBin";
	}

	/**
	 * @return int
	 */
	public function getDefaultSize() : int{
		return 27;
	}

	/**
	 * @return int
	 */
	public function getNetworkType() : int{
		return WindowTypes::CONTAINER;
	}
}
