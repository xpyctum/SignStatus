<?php

namespace SignStatus;

use pocketmine\nbt\tag\StringTag;
use pocketmine\scheduler\PluginTask;
use pocketmine\tile\Sign;

class Task extends PluginTask{
    private $plugin;
    private $countable;

    public function __construct(SignStatus $plugin){
        parent::__construct($plugin);
        $this->plugin = $plugin;
        $this->countable = 0;
    }

    public function onRun($currentTick){
        $format = $this->plugin->format->getAll();
        foreach ($this->plugin->getServer()->getLevels() as $levels) {
            foreach ($levels->getTiles() as $tile) {
                if ($tile instanceof Sign) {
                    if (strtolower($tile->getText()[0]) == strtolower($this->plugin->format->getAll()["format"][1])) {
                        $tps = $this->plugin->getServer()->getTicksPerSecond();
                        $p = count($this->plugin->getServer()->getOnlinePlayers());
                        $full = $this->plugin->getServer()->getMaxPlayers();
                        $count = $this->countable++; //For debug
                        $load = $this->plugin->getServer()->getTickUsage();
                        $level = $tile->getLevel()->getName();
                        $index = [];
                        for ($x = 0; $x <= 3; $x++) {
                            $v = $format["format"][$x + 1];
                            $v = str_replace("{ONLINE}", $p, $v);
                            $v = str_replace("{MAX_ONLINE}", $full, $v);
                            $v = str_replace("{WORLD_NAME}", $level, $v);
                            $v = str_replace("{TPS}", $tps, $v);
                            $v = str_replace("{SERVER_LOAD}", $load, $v);
                            $index[$x] = $v;
                        }
                        $tile->setText($index[0], $index[1], $index[2], $index[3]);
                    }
                }
            }
        }
    }
}
