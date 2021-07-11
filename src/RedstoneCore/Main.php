<?php
namespace RedstoneCore;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\level\particle\Particle;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\level\particle\HeartParticle;
use pocketmine\level\particle\PortalParticle;
use pocketmine\level\particle\SmokeParticle;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\level\particle\WaterDripParticle;
use pocketmine\level\particle\HugeExplodeParticle;
use pocketmine\level\particle\FlameParticle;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\event\player\PlayerKickEvent;
use pocketmine\level\sound\BlazeShootSound;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\level\sound\ClickSound;
use pocketmine\level\sound\PopSound;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\scheduler\PluginTask;
use pocketmine\permission\PermissionAttachment;
use pocketmine\permission\Permissible;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionRemovedExecuter;
use pocketmine\utils\Config;
use jasonwynn10\ScoreboardAPI\Scoreboard;
use jasonwynn10\ScoreboardAPI\ScoreboardAPI;
use jasonwynn10\ScoreboardAPI\ScoreboardEntry;

use pocketmine\event\Listener;


class Main extends PluginBase implements Listener{
    // Public Var.
    public $heart = [];
    public $flame = [];
    public $smoke = [];
    public $portal = [];
    public $level = [];
    public $reset = [];
    private $att = [];
    public function onEnable(){
        @mkdir($this->getDataFolder() . "players/");
        $this->getLogger()->info("This Server Runs A [RMC AI] C");
        foreach(["steve.png", "steve.geo.json"] as $file) {
			$this->saveResource($file);
		}
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
    public function scoreboard($player){
    }
    public function onDisable(){
        $this->getLogger()->info("RedstoneMc Core Disabled .....");
    }
    public function onCommand(CommandSender $sender, Command $cmd, String $label, Array $args) : bool {
        switch($cmd->getName()){
            case "hub":
                if($sender instanceof Player){
                    $sender->sendMessage("§4Requesting Teleport to Hub ........");
                    $sender->getInventory()->clearAll();
                    $sender->getArmorInventory()->clearAll();
                    $this->getServer()->loadLevel("world");
                    $sender->teleport($this->getServer()->getLevelByName("world")->getSafeSpawn());
                    $sender->sendMessage("§aYou Have Been Teleported To The Lobby§f");
                    $sender->getLevel()->addSound(new EndermanTeleportSound($sender));
                    $slot5 = Item::get(345, 0, 1);
                    $slot9 = Item::get(152, 0, 1);
                    $slot = Item::get(264, 0, 1);
                    $slot5->setCustomName("§l§cGames");
                    $slot9->setCustomName("§l§bGadgets");
                    $slot->setCustomName("§l§aProfile");
                    $sender->getInventory()->setItem(4, $slot5);
                    $sender->getInventory()->setItem(0, $slot9);
                    $sender->getInventory()->setItem(8, $slot);
                    $sender->setGamemode(0);
                    return true;
                }
            case "rules":
             if($sender instanceof Player){
                 $sender->sendMessage("§4RedstoneMc §aRules \n 1-No Spamming\n 2-No Bad Name Or Skin\n3-DON'T hack\n 4-No Swearing ");
                 return true;
                } 
            case "welcome":
             if($sender instanceof Player){
                 $sender->sendMessage("Welcome");
                 return true;
             }
            case "staffmode":
             if($sender instanceof Player){

                 if($sender->hasPermission("staff.rmc")){
                     $this->flyform($sender);
                     return true;
                 }else {
                     $sender->sendMessage("§4You Don't Have Permissions");
                 }
                 return true;
             }
            
            
            case "killwand":
                if($sender->hasPermission("kill.rmc")){
                    $sender->getInventory()->clearAll();
                    $sender->getArmorInventory()->clearAll();
                    $killwandenchant = Enchantment::getEnchantment(9);
                    $killwandenchantm = new EnchantmentInstance($killwandenchant, 200);
                    $killwand = Item::get(369, 0, 1);
                    $killwand->addEnchantment($killwandenchantm);
                    $sender->getInventory()->addItem($killwand);
                    return true;
                }

            case "rban":
                if($sender->hasPermission("staff.rmc")){
                    if(isset($args[0])){
                        if($targete = $this->getServer()->getPlayer($args[0])){
                            $targete = $this->getServer()->getPlayer($args[0]);
                            $target = $targete->getName();
                            $banner = $sender->getName();
                            
                            $targete->kick("§l[§4RMC System§f] >> §cYou Have Been §4Permently §cBanned From \n§r§4RedstoneMc §6Network §l§cBy §r§e" . $banner);
                            $targete->setBanned(true);
                            
                            foreach($this->getServer()->getOnlinePlayers() as $mods){
                                if($mods->hasPermission("staff.rmc")){
                                    $mods->sendMessage("§l[§4RMC System§f] >> §c" . $target . " Have Been Banned By §a" . $banner);
                                }
                            }
                            if(!isset($args[1])){
                                $sender->sendMessage("§l[§4RMC System§f] >> §cUsage: /rban {player name} ");
                            }
                        } else {
                            $sender->sendMessage("§l[§4RMC System§f] >> §cThis Player isn't Online");
                        }
                            
                            
                    }
                    
                    
                    
                } 
                return true;
            case "report":
                if(isset($args[0])){
                    
                    $reporte = $this->getServer()->getPlayer($args[0]);
                    if($reporte = $this->getServer()->getPlayer($args[0])){

                        $reported = $reporte->getName();
                        $reporter = $sender->getName();
                        
                        $game = $sender->getLevel()->getName();
                        $reasonreported = $args[1];
                        $sender->sendMessage("§l[§4RMC System§f] >> §aYour Report Request Has Been Sent to Staff...");
                        
                        if(empty($reasonreported)){
                            $sender->sendMessage("§l[§4RMC System§f] >> §cUsage: /report {player name} {reason}");
                        }
                        foreach($this->getServer()->getOnlinePlayers() as $mods){
                            if($mods->hasPermission("staff.rmc")){
                                $mods->sendMessage("§l[§4RMC System§f] >> §r§8|§4New Report §8| §a" . $reported . " §bHave Been Reported By §a" . $reporter . " §a in §e" . $game . " §cFor §a" . $reasonreported);
                            }
                        }
                        if(!isset($args[1])){
                            $sender->sendMessage("§l[§4RMC System§f] >> §cUsage: /report {player name} {reason}");
                        }
                    } else {
                        $sender->sendMessage("§l[§4RMC System§f] >> §cThis Player isn't Online");
                    }
                    
                }
                return true;
            case "mute":
                if($sender->hasPermission("staff.rmc")){
                    if(isset($args[0])){
                        if($targete = $this->getServer()->getPlayer($args[0])){
                            $playermuted = $this->getServer()->getPlayer($args[0]);
                            $playerm = $playermuted->getName();
                            $this->ranksdata = new Config($this->getDataFolder() . "players/" . strtolower($playermuted->getName()), Config::YAML, array(
                                "Player Name" => $playermuted->getName(),
                                "Mute Status" => "False"
                            ));
                            $this->ranksdata->set("Mute Status", "True");
                            $this->ranksdata->save();
                            $sender->sendMessage("§l[§4RMC System§f] >> §r§4 " . $playerm . " §cHas Muted");
                            foreach($this->getServer()->getOnlinePlayers() as $mods){
                                if($mods->hasPermission("staff.rmc")){
                                    $mods->sendMessage("§l[§4RMC System§f] >> §r§4 " . $playerm . " §cHas Muted");
                                }
                            }
                            if(!isset($args[0])){
                                $sender->sendMessage("§l[§4RMC System§f] >> §cUsage: /mute {player name}");
                            }
                        } else{
                            $sender->sendMessage("§l[§4RMC System§f] >> §cThis Player isn't Online");
                        } 
                    } 
                }
                return true;

                case "unmute":
                    if($sender->hasPermission("staff.rmc")){
                        if(isset($args[0])){
                            if($targete = $this->getServer()->getPlayer($args[0])){
                                $playermuted = $this->getServer()->getPlayer($args[0]);
                                $playerm = $playermuted->getName();
                                $this->ranksdata = new Config($this->getDataFolder() . "players/" . strtolower($playermuted->getName()), Config::YAML, array(
                                    "Player Name" => $playermuted->getName(),
                                    "Mute Status" => "False"
                                ));
                                $this->ranksdata->set("Mute Status", "False");
                                $this->ranksdata->save();
                                $sender->sendMessage("§l[§4RMC System§f] >> §r§4 " . $playerm . " §cHas UnMuted");
                                foreach($this->getServer()->getOnlinePlayers() as $mods){
                                    if($mods->hasPermission("staff.rmc")){
                                        $mods->sendMessage("§l[§4RMC System§f] >> §r§4 " . $playerm . " §cHas UnMuted");
                                    }
                                }
                                if(!isset($args[0])){
                                    $sender->sendMessage("§l[§4RMC System§f] >> §cUsage: /unmute {player name}");
                                }
                            } else{
                                $sender->sendMessage("§l[§4RMC System§f] >> §cThis Player isn't Online");
                            }
                        } 
                    }
                    return true;    
                        
                        
                       
                    
                
            case "event":
                
                if($sender->hasPermission("owner.rmc")){

                    foreach($this->getServer()->getOnlinePlayers() as $players){
                        $players->getLevel()->addSound(new ClickSound($players));
                        $players->sendMessage("§l§6Event START in §cLobby");
                        $players->getInventory()->clearAll();
                        $players->getArmorInventory()->clearAll();
                        $players->getServer()->loadLevel("world");
                        $players->teleport($this->getServer()->getLevelByName("world")->getSafeSpawn());
                        $players->addTitle("§l§6Event !\n§bServer Developed\n By iAbdo1731");
                        $slot5 = Item::get(345, 0, 1);
                        $slot9 = Item::get(152, 0, 1);
                        $slot = Item::get(264, 0, 1);
                        $slot5->setCustomName("§l§cGames");
                        $slot9->setCustomName("§l§bGadgets");
                        $slot->setCustomName("§l§aProfile");
                        $players->getInventory()->setItem(4, $slot5);
                        $players->getInventory()->setItem(0, $slot9);
                        $players->getInventory()->setItem(8, $slot);
                        }
                        
                    }
                    return true;
            case "store":
                $sender->sendMessage("§l[§4RMC System§f] >> §aDo You Want to Support the Server , See the Best Offers at https://redstonemc-network.tebex.io");
                return true;    
            case "discord":
                $sender->sendMessage("§l[§4RMC System§f] >> §r§a Join Us in Our Discord discord.gg/8qmvmJyvgN");
                return true;            
            
            case "rteleport":
                
                    if(isset($args[0])){
                        if($sender instanceof Player){
                            switch(strtolower($args[0])){
                                case "knock":
                                    $sender->sendMessage("§4Requesting Teleport to §l§bKnock GearUP v3.0 ........");
                                    $sender->getInventory()->clearAll();
                                    $sender->getArmorInventory()->clearAll();
                                    $this->getServer()->loadLevel("knockgear");
                                    $sender->teleport($this->getServer()->getLevelByName("knockgear")->getSafeSpawn());
                                    $sender->sendMessage("§aYou Have Been Teleported To §bKnock GearUP v3.0f");
                                    return true;
                                case "bow":
                                     
                                     return true;
                                case "knockarena":
                                    $sender->sendMessage("§l[§4RMC System§f] >> §r§4Requesting Teleport to §l§bKnock Arena v3.0 ........");
                                    
                                    $this->getServer()->loadLevel("knock");
                                    $sender->teleport($this->getServer()->getLevelByName("knock")->getSafeSpawn());
                                    $sender->sendMessage("§aYou Have Been Teleported To §bKnock Arena v3.0f");
                                    return true;

                                case "kitpvp":
                                    $sender->sendMessage("§l[§4RMC System§f] >> §r§4Requesting Teleport to §cKitPvP GearUP v3.0§4 ........");
                                    $sender->getInventory()->clearAll();
                                    $sender->getArmorInventory()->clearAll();
                                    $this->getServer()->loadLevel("kitpvpgear");
                                    $sender->teleport($this->getServer()->getLevelByName("kitpvpgear")->getSafeSpawn());
                                    $sender->sendMessage("§l[§4RMC System§f] >> §r§aYou Have Been Teleported To The §cKitPvP GearUP§f");
                                    return true;
                                case "skypvp":
                                    
                                    return true;

                                case "kitarena":
                                    $sender->sendMessage("§l[§4RMC System§f] >> §r§4Requesting Teleport to §cKitPvP Arena v3.0§4 ........");
                                    
                                   
                                    $this->getServer()->loadLevel("kitpvp");
                                    $sender->teleport($this->getServer()->getLevelByName("kitpvp")->getSafeSpawn());
                                    $sender->sendMessage("§l[§4RMC System§f] >> §r§aYou Have Been Teleported To The §cKitPvP Arena§f");
                                    return true;
                                
                                case "prisonpvp":
                                    $sender->sendMessage("§l[§4RMC System§f] >> §r§aRequesting Teleport to §l§aPrisonPvP §4 ........");
                                    $sender->getInventory()->clearAll();
                                    $sender->getArmorInventory()->clearAll();
                                    $this->getServer()->loadLevel("prisonpvp");
                                    $sender->teleport($this->getServer()->getLevelByName("prisonpvp")->getSafeSpawn());
                                    $sender->sendMessage("§l[§4RMC System§f] >> §r§aYou Have Been Teleported To The §l§aPrisonPvP §f");
                                    return true;
                                case "prison":
                                    $sender->sendMessage("§l[§4RMC System§f] >> §r§aRequesting Teleport to §l§aPrison §4 ........");
                                    $sender->getInventory()->clearAll();
                                    $sender->getArmorInventory()->clearAll();
                                    $this->getServer()->loadLevel("prison");
                                    $sender->teleport($this->getServer()->getLevelByName("prison")->getSafeSpawn());
                                    $sender->sendMessage("§l[§4RMC System§f] >> §r§aYou Have Been Teleported To The §l§aPrison §f");
                                    return true;
                                case "plots":
                                    $sender->sendMessage("§l[§4RMC System§f] >> §r§aRequesting Teleport to §l§dCreative§4 ........");
                                    $sender->getInventory()->clearAll();
                                    $sender->getArmorInventory()->clearAll();
                                    $this->getServer()->loadLevel("plots");
                                    $sender->teleport($this->getServer()->getLevelByName("plots")->getSafeSpawn());
                                    $sender->setGamemode(1);
                                    $sender->sendMessage("§l[§4RMC System§f] >> §r§aYou Have Been Teleported To The §l§dCreative§f");
                                    return true;
                            }
                            return true;
                        }
                    }
            case "setrank":
                if($sender->hasPermission("owner.rmc")){
                    if(isset($args[0])){
                        $playertargeted = $this->getServer()->getPlayer($args[0]);
                        $this->ranksdata = new Config($this->getDataFolder() . "players/" . strtolower($playertargeted->getName()), Config::YAML, array(
                            "Player Name" => $playertargeted->getName(),
                            "Rank" => "Member"
                        ));
                        
                        $playerrank = $args[1];
                        if($playerrank == "member"){
                            $this->ranksdata->set("Rank", "Member");
                            $this->ranksdata->save();
                            $playertargeted->setDisplayName("§8|§l§7Member§r§8|§7 " . $playertargeted->getName());
                            $this->att($playertargeted)->unsetPermission("vip.rmc");
                            $this->att($playertargeted)->unsetPermission("vipx.rmc");
                            $this->att($playertargeted)->unsetPermission("mvp.rmc");
                            $this->att($playertargeted)->unsetPermission("staff.rmc");
                            $this->att($playertargeted)->unsetPermission("kill.rmc");
                            $this->att($playertargeted)->unsetPermission("owner.rmc");
                            $this->att($playertargeted)->unsetPermission("staffm.rmc");
                            $playertargeted->sendMessage("§l[§4RMC System§f] >> §r§aSuccessfully Your Rank Has Changed to §7Member");
                        }
                        if($playerrank == "vip"){
                            $this->ranksdata->set("Rank", "VIP");
                            $this->ranksdata->save();
                            $playertargeted->setDisplayName("§8|§l§bVIP§r§8|§b " . $playertargeted->getName());
                            $this->att($playertargeted)->setPermission("vip.rmc", true, "vip.rmc");
                            $this->att($playertargeted)->unsetPermission("staff.rmc");
                            $this->att($playertargeted)->unsetPermission("kill.rmc");
                            $this->att($playertargeted)->unsetPermission("owner.rmc");
                            $this->att($playertargeted)->unsetPermission("staffm.rmc");
                            $playertargeted->sendMessage("§l[§4RMC System§f] >> §r§aSuccessfully Your Rank Has Changed to §bVIP");
                        }
                        if($playerrank == "vip+"){
                            $this->ranksdata->set("Rank", "VIP+");
                            $this->ranksdata->save();
                            $playertargeted->setDisplayName("§8|§l§6VIP+§r§8|§6 " . $playertargeted->getName());
                            $this->att($playertargeted)->setPermission("vipx.rmc", true, "vipx.rmc");
                            $this->att($playertargeted)->unsetPermission("staff.rmc");
                            $this->att($playertargeted)->unsetPermission("kill.rmc");
                            $this->att($playertargeted)->unsetPermission("owner.rmc");
                            $this->att($playertargeted)->unsetPermission("staffm.rmc");
                            $playertargeted->sendMessage("§l[§4RMC System§f] >> §r§aSuccessfully Your Rank Has Changed to §6VIP+");
                        }
                        
                        
                    

                    if($playerrank == "mvp"){
                        $this->ranksdata->set("Rank", "MVP");
                        $this->ranksdata->save();
                        $playertargeted->setDisplayName("§8|§l§5MVP§r§8|§5 " . $playertargeted->getName());
                        $this->att($playertargeted)->setPermission("mvp.rmc", true, "mvp.rmc");
                        $this->att($playertargeted)->unsetPermission("staff.rmc");
                        $this->att($playertargeted)->unsetPermission("kill.rmc");
                        $this->att($playertargeted)->unsetPermission("owner.rmc");
                        $this->att($playertargeted)->unsetPermission("staffm.rmc");
                        $playertargeted->sendMessage("§l[§4RMC System§f] >> §r§aSuccessfully Your Rank Has Changed to §5MVP");
                    }

                    if($playerrank == "yt"){
                        $this->ranksdata->set("Rank", "Youtuber");
                        $this->ranksdata->save();
                        $playertargeted->setDisplayName("§8|§l§cYou§fTuber§r§8|§5 " . $playertargeted->getName());
                        $this->att($playertargeted)->setPermission("vipx.rmc", true, "vipx.rmc");
                        $this->att($playertargeted)->setPermission("mvp.rmc", true, "mvp.rmc");
                        $this->att($playertargeted)->unsetPermission("staff.rmc");
                        $this->att($playertargeted)->unsetPermission("kill.rmc");
                        $this->att($playertargeted)->unsetPermission("owner.rmc");
                        $this->att($playertargeted)->unsetPermission("staffm.rmc");
                        $playertargeted->sendMessage("§l[§4RMC System§f] >> §r§aSuccessfully Your Rank Has Changed to §cYou§fTuber");
                    }

                    if($playerrank == "mod"){
                        $this->ranksdata->set("Rank", "Mod");
                        $this->ranksdata->save();
                        $playertargeted->setDisplayName("§8|§l§cMod§r§8|§c " . $playertargeted->getName());
                        $this->att($playertargeted)->setPermission("mvp.rmc", true, "mvp.rmc");
                        $this->att($playertargeted)->setPermission("staff.rmc", true, "staff.rmc");
                        $this->att($playertargeted)->unsetPermission("staffm.rmc");
                        $this->att($playertargeted)->unsetPermission("owner.rmc");
                        $playertargeted->sendMessage("§l[§4RMC System§f] >> §r§aSuccessfully Your Rank Has Changed to §cModerator");
                    }

                    if($playerrank == "admin"){
                        $this->ranksdata->set("Rank", "Admin");
                        $this->ranksdata->save();
                        $playertargeted->setDisplayName("§8|§l§cAdmin§r§8|§c " . $playertargeted->getName());
                        $this->att($playertargeted)->setPermission("mvp.rmc", true, "mvp.rmc");
                        $this->att($playertargeted)->setPermission("staff.rmc", true, "staff.rmc");
                        
                        $playertargeted->sendMessage("§l[§4RMC System§f] >> §r§aSuccessfully Your Rank Has Changed to §4Admin");
                    }

                    if($playerrank == "staffmanager"){
                        $this->ranksdata->set("Rank", "StaffManager");
                        $this->ranksdata->save();
                        $playertargeted->setDisplayName("§8|§l§aStaffManager§r§8|§a " . $playertargeted->getName());
                        $this->att($playertargeted)->setPermission("mvp.rmc", true, "mvp.rmc");
                        $this->att($playertargeted)->setPermission("staff.rmc", true, "staff.rmc");
                        $this->att($playertargeted)->setPermission("kill.rmc", true, "kill.rmc");
                        $this->att($playertargeted)->setPermission("staffm.rmc", true, "staffm.rmc");
                        $this->att($playertargeted)->setPermission("owner.rmc", true, "owner.rmc");
                        
                        $playertargeted->sendMessage("§l[§4RMC System§f] >> §r§aSuccessfully Your Rank Has Changed to §aStaffManager");  
                    }

                    if($playerrank == "manager"){
                        $this->ranksdata->set("Rank", "Manager");
                        $this->ranksdata->save();
                        $playertargeted->setDisplayName("§8|§l§4Manager§r§8|§c " . $playertargeted->getName());
                        $this->att($playertargeted)->setPermission("mvp.rmc", true, "mvp.rmc");
                        $this->att($playertargeted)->setPermission("staff.rmc", true, "staff.rmc");
                        $this->att($playertargeted)->setPermission("kill.rmc", true, "kill.rmc");
                        $this->att($playertargeted)->unsetPermission("owner.rmc");
                        $playertargeted->sendMessage("§l[§4RMC System§f] >> §r§aSuccessfully Your Rank Has Changed to §4Manager");
                    }

                    if($playerrank == "coowner"){
                        $this->ranksdata->set("Rank", "CoOwner");
                        $this->ranksdata->save();
                        $playertargeted->setDisplayName("§8|§l§eCo-Owner§r§8|§e " . $playertargeted->getName());
                        $this->att($playertargeted)->setPermission("mvp.rmc", true, "mvp.rmc");
                        $this->att($playertargeted)->setPermission("staff.rmc", true, "staff.rmc");
                        $this->att($playertargeted)->setPermission("kill.rmc", true, "kill.rmc");
                        $this->att($playertargeted)->setPermission("owner.rmc", true, "owner.rmc");
                        $this->att($playertargeted)->unsetPermission("staffm.rmc");
                        $playertargeted->sendMessage("§l[§4RMC System§f] >> §r§aSuccessfully Your Rank Has Changed to §eCo-OWNER");
                        
                    }

                    if($playerrank == "owner"){
                        $this->ranksdata->set("Rank", "Owner");
                        $this->ranksdata->save();
                        $playertargeted->setDisplayName("§8|§l§4Owner§r§8|§4 " . $playertargeted->getName());
                        $this->att($playertargeted)->setPermission("mvp.rmc", true, "mvp.rmc");
                        $this->att($playertargeted)->setPermission("staff.rmc", true, "staff.rmc");
                        $this->att($playertargeted)->setPermission("kill.rmc", true, "kill.rmc");
                        $this->att($playertargeted)->setPermission("owner.rmc", true, "owner.rmc");
                        
                        $playertargeted->sendMessage("§l[§4RMC System§f] >> §r§aSuccessfully Your Rank Has Changed to §4OWNER");

                    }

                    if($this->ranksdata->get("Rank") == "Member"){
                        $playertargeted->setDisplayName("§8|§l§7Member§r§8|§7 " . $playertargeted->getName());
                        $playertargeted->setNameTag("§7 " . $playertargeted->getName());
                        $this->att($playertargeted)->unsetPermission("vip.rmc");
                        $this->att($playertargeted)->unsetPermission("staff.rmc");
                        $this->att($playertargeted)->unsetPermission("kill.rmc");
                        $this->att($playertargeted)->unsetPermission("owner.rmc");
                        $this->att($playertargeted)->unsetPermission("pocketmine.command.gamemode");
                    }

                    if($this->ranksdata->get("Rank") == "VIP"){
                        $playertargeted->setDisplayName("§8|§l§bVIP§r§8|§b " . $playertargeted->getName());
                        $playertargeted->setNameTag("§b " . $playertargeted->getName());
                        $this->att($playertargeted)->setPermission("vip.rmc", true, "vip.rmc");
                        $this->att($playertargeted)->unsetPermission("staff.rmc");
                        $this->att($playertargeted)->unsetPermission("kill.rmc");
                        $this->att($playertargeted)->unsetPermission("pocketmine.command.gamemode");
                        $this->att($playertargeted)->unsetPermission("owner.rmc");
                    }

                    if($this->ranksdata->get("Rank") == "VIP+"){
                        $playertargeted->setDisplayName("§8|§l§6VIP+§r§8|§6 " . $playertargeted->getName());
                        $playertargeted->setNameTag("§6 " . $playertargeted->getName());
                        $this->att($playertargeted)->setPermission("vip.rmc", true, "vip.rmc");
                        $this->att($playertargeted)->setPermission("vipx.rmc", true, "vipx.rmc");
                        $this->att($playertargeted)->unsetPermission("staff.rmc");
                        $this->att($playertargeted)->unsetPermission("kill.rmc");
                        $this->att($playertargeted)->unsetPermission("owner.rmc");
                        $this->att($playertargeted)->unsetPermission("pocketmine.command.gamemode");
                    }

                    if($this->ranksdata->get("Rank") == "MVP"){
                        $playertargeted->setDisplayName("§8|§l§aEmerald§r§8|§a " . $playertargeted->getName());
                        $playertargeted->setNameTag("§a " . $playertargeted->getName());
                        $this->att($playertargeted)->setPermission("vip.rmc", true, "vip.rmc");
                        $this->att($playertargeted)->setPermission("mvp.rmc", true, "mvp.rmc");
                        $this->att($playertargeted)->unsetPermission("staff.rmc");
                        $this->att($playertargeted)->unsetPermission("kill.rmc");
                        $this->att($playertargeted)->unsetPermission("owner.rmc");
                        $this->att($playertargeted)->unsetPermission("pocketmine.command.gamemode");
                    }

                    if($this->ranksdata->get("Rank") == "Youtuber"){
                        $playertargeted->setDisplayName("§8|§l§cYou§ftuber§r§8|§c " . $playertargeted->getName());
                        $playertargeted->setNameTag("§8|§l§cYou§ftuber§r§8|§c " . $playertargeted->getName());
                        $this->att($playertargeted)->setPermission("vip.rmc", true, "vip.rmc");
                        $this->att($playertargeted)->setPermission("mvp.rmc", true, "mvp.rmc");
                        $this->att($playertargeted)->unsetPermission("staff.rmc");
                        $this->att($playertargeted)->unsetPermission("kill.rmc");
                        $this->att($playertargeted)->unsetPermission("owner.rmc");
                        $this->att($playertargeted)->unsetPermission("pocketmine.command.gamemode");
                    }

                    if($this->ranksdata->get("Rank") == "Mod"){
                        $playertargeted->setDisplayName("§8|§l§cMod§r§8|§c " . $playertargeted->getName());
                        $playertargeted->setNameTag("§8|§l§cMod§r§8|§c " . $playertargeted->getName());
                        $this->att($playertargeted)->setPermission("vip.rmc", true, "vip.rmc");
                        $this->att($playertargeted)->setPermission("mvp.rmc", true, "mvp.rmc");
                        $this->att($playertargeted)->setPermission("staff.rmc", true, "staff.rmc");
                        $this->att($playertargeted)->setPermission("pocketmine.command.gamemode", true, "pocketmine.command.gamemode");
                    }

                    if($this->ranksdata->get("Rank") == "Admin"){
                        $playertargeted->setDisplayName("§8|§l§4Admin§r§8|§c " . $playertargeted->getName());
                        $playertargeted->setNameTag("§8|§l§4Admin§r§8|§c " . $playertargeted->getName());
                        $this->att($playertargeted)->setPermission("vip.rmc", true, "vip.rmc");
                        $this->att($playertargeted)->setPermission("mvp.rmc", true, "mvp.rmc");
                        $this->att($playertargeted)->setPermission("staff.rmc", true, "staff.rmc");
                        $this->att($playertargeted)->setPermission("pocketmine.command.gamemode", true, "pocketmine.command.gamemode");
                        
                    }

                    if($this->ranksdata->get("Rank") == "StaffManager"){
                        $playertargeted->setDisplayName("§8|§l§aStaffManager§r§8|§c " . $playertargeted->getName());
                        $playertargeted->setNameTag("§8|§l§aStaffManager§r§8|§c " . $playertargeted->getName());
                        $this->att($playertargeted)->setPermission("vip.rmc", true, "vip.rmc");
                        $this->att($playertargeted)->setPermission("mvp.rmc", true, "mvp.rmc");
                        $this->att($playertargeted)->setPermission("staff.rmc", true, "staff.rmc");
                        $this->att($playertargeted)->setPermission("kill.rmc", true, "kill.rmc");
                        $this->att($playertargeted)->setPermission("staffm.rmc", true, "staffm.rmc");
                        $this->att($playertargeted)->setPermission("owner.rmc", true, "owner.rmc");
                        $this->att($playertargeted)->setPermission("pocketmine.command.gamemode", true, "pocketmine.command.gamemode");
                    }

                    if($this->ranksdata->get("Rank") == "Manager"){
                        $playertargeted->setDisplayName("§8|§l§aManager§r§8|§c " . $playertargeted->getName());
                        $playertargeted->setNameTag("§8|§l§aManager§r§8|§c " . $playertargeted->getName());
                        $this->att($playertargeted)->setPermission("vip.rmc", true, "vip.rmc");
                        $this->att($playertargeted)->setPermission("mvp.rmc", true, "mvp.rmc");
                        $this->att($playertargeted)->setPermission("staff.rmc", true, "staff.rmc");
                        $this->att($playertargeted)->setPermission("kill.rmc", true, "kill.rmc");
                        $this->att($playertargeted)->setPermission("staffm.rmc", true, "staffm.rmc");
                        $this->att($playertargeted)->setPermission("pocketmine.command.gamemode", true, "pocketmine.command.gamemode");
                    }

                    if($this->ranksdata->get("Rank") == "CoOwner"){
                        $playertargeted->setDisplayName("§8|§l§eCo-Owner§r§8|§c " . $playertargeted->getName());
                        $playertargeted->setNameTag("§8|§l§eCo-Owner§r§8|§c " . $playertargeted->getName());
                        $this->att($playertargeted)->setPermission("vip.rmc", true, "vip.rmc");
                        $this->att($playertargeted)->setPermission("mvp.rmc", true, "mvp.rmc");
                        $this->att($playertargeted)->setPermission("staff.rmc", true, "staff.rmc");
                        $this->att($playertargeted)->setPermission("kill.rmc", true, "kill.rmc");
                        $this->att($playertargeted)->setPermission("owner.rmc", true, "owner.rmc");
                        $this->att($playertargeted)->setPermission("staffm.rmc", true, "staffm.rmc");
                        $this->att($playertargeted)->setPermission("pocketmine.command.gamemode", true, "pocketmine.command.gamemode");
                    }

                    if($this->ranksdata->get("Rank") == "Owner"){
                        $playertargeted->setDisplayName("§8|§l§4Owner§r§8|§4 " . $playertargeted->getName());
                        $playertargeted->setNameTag("§8|§l§4Owner§r§8|§4 " . $playertargeted->getName());
                        $this->att($playertargeted)->setPermission("vip.rmc", true, "vip.rmc");
                        $this->att($playertargeted)->setPermission("mvp.rmc", true, "mvp.rmc");
                        $this->att($playertargeted)->setPermission("staff.rmc", true, "staff.rmc");
                        $this->att($playertargeted)->setPermission("kill.rmc", true, "kill.rmc");
                        $this->att($playertargeted)->setPermission("owner.rmc", true, "owner.rmc");
                        $this->att($playertargeted)->setPermission("staffm.rmc", true, "staffm.rmc");
                        $this->att($playertargeted)->setPermission("pocketmine.command.gamemode", true, "pocketmine.command.gamemode");
                    }
                    return true;
                }
            }
            
            
           
            case "setjoinmsg":
                if(isset($args[0])){
                    $message = $args[0];
                    $this->ranksdata = new Config($this->getDataFolder() . "players/" . strtolower($sender->getName()), Config::YAML, array(
                        "Player Name" => $sender->getName(),
                        "Join MSG" => "Default"
                    ));
                    if($sender->hasPermission("vip.rmc")){
                        $this->ranksdata->set("Join MSG", $message);
                        $this->ranksdata->save();
                    } else {
                        $sender->sendMessage("§l[§4RMC System§f] >> §cSorry You Should Have A §6Heigher Rank§c To Use That");
                    }
                    




                    if(!isset($args[0])){
                        $sender->sendMessage("§l[§4RMC System§f] >> §r§cUsage : /setjoinmsg [message]");
                    }
                }
                
        }
        return true;
    }
    
    
        /*if($game == "ffa"){
            if($player->getHealth() < 3){
                $e->setCancelled();
                $player->sendMessage("§l[§4RMC System§f] >> §r§aRespawning....");
                $player->setHealth(20);
                $player->getInventory()->clearAll();
                $player->getArmorInventory()->clearAll();
                $this->getServer()->loadLevel("ffa");
                $player->teleport($this->getServer()->getLevelByName("ffa")->getSafeSpawn());
            }
        }**/
        
    
    public function onJoin(PlayerJoinEvent $e){
        $player = $e->getPlayer();
        $this->ranksdata = new Config($this->getDataFolder() . "players/" . strtolower($player->getName()), Config::YAML, array(
            "Player Name" => $player->getName(),
            "Rank" => "Member",
            "Ban Status =>" => "False",
            "Mute Status =>" => "False",
            "Join MSG =>" => "Default",
            "Leave MSG =>" => "Default"
        ));
        $api = $this->getServer()->getPluginManager()->getPlugin("ScoreboardAPI");
        $scoreboard = $api->createScoreboard("objective", "§l§4RedstoneMc §6Network");
        $line = 1; // line number
        $score = 1; // current score
        $type = ScoreboardEntry::TYPE_FAKE_PLAYER; // other types are TYPE_PLAYER and TYPE_ENTITY
        $identifier = "line 1"; // this is a string for fake players but must be an entity id for other types
        /** @var Scoreboard $scoreboard */
        $entry = $scoreboard->createEntry($line, $score, $type, $identifier);
        $scoreboard->addEntry($entry);
        $api->sendScoreboard($scoreboard); 

        $player->getLevel()->addSound(new BlazeShootSound($player));
        $player->getInventory()->clearAll();
        $this->getServer()->loadLevel("world");
        $player->getArmorInventory()->clearAll();
        $player->teleport($this->getServer()->getLevelByName("world")->getSafeSpawn());
        if($this->ranksdata->get("Join MSG") == "Default"){
            $this->getServer()->broadcastMessage("A Big Warn [§3+§f] " . $player->getName() . " §eJoined§f §l§aRedstoneMc§f");
        } 
        if($this->ranksdata->get("Join MSG") != "Default"){
            $this->getServer()->broadcastMessage("A Big Warn [§3+§f] " . $player->getName() .  $this->ranksdata->get("Join MSG"));
        } 
        $slot5 = Item::get(345, 0, 1);
        $slot9 = Item::get(152, 0, 1);
        $slot = Item::get(264, 0, 1);
        $slot5->setCustomName("§l§cGames");
        $slot9->setCustomName("§l§bGadgets");
        $slot->setCustomName("§l§aProfile");
        $player->getInventory()->setItem(4, $slot5);
        $player->getInventory()->setItem(0, $slot9);
        $player->getInventory()->setItem(8, $slot);
        
        if($this->ranksdata->get("Rank") == "Member"){
            $player->setDisplayName("§8|§l§7Member§r§8|§7 " . $player->getName());
            $player->setNameTag("§7 " . $player->getName());
            $this->att($player)->unsetPermission("vip.rmc");
            $this->att($player)->unsetPermission("vipx.rmc");
            $this->att($player)->unsetPermission("mvp.rmc");
            $this->att($player)->unsetPermission("staff.rmc");
            $this->att($player)->unsetPermission("kill.rmc");
            $this->att($player)->unsetPermission("owner.rmc");
            $this->att($player)->unsetPermission("staffm.rmc");
            $this->att($player)->unsetPermission("pocketmine.command.gamemode");
        }
        if($this->ranksdata->get("Rank") == "VIP"){
            $player->setDisplayName("§8|§l§bDiamond§r§8|§b " . $player->getName());
            $player->setNameTag("§b " . $player->getName());
            $this->att($player)->setPermission("vip.rmc", true, "vip.rmc");
            $this->att($player)->unsetPermission("staff.rmc");
            $this->att($player)->unsetPermission("kill.rmc");
            $this->att($player)->unsetPermission("owner.rmc");
            $this->att($player)->unsetPermission("staffm.rmc");
            $this->att($player)->unsetPermission("pocketmine.command.gamemode");
        }
        if($this->ranksdata->get("Rank") == "VIP+"){
            $player->setDisplayName("§8|§l§6Gold§r§8|§6 " . $player->getName());
            $player->setNameTag("§6 " . $player->getName());
            $this->att($player)->setPermission("vip.rmc", true, "vip.rmc");
            $this->att($player)->setPermission("vipx.rmc", true, "vipx.rmc");
            $this->att($player)->unsetPermission("staff.rmc");
            $this->att($player)->unsetPermission("kill.rmc");
            $this->att($player)->unsetPermission("owner.rmc");
            $this->att($player)->unsetPermission("staffm.rmc");
            $this->att($player)->unsetPermission("pocketmine.command.gamemode");
        }
        if($this->ranksdata->get("Rank") == "MVP"){
            $player->setDisplayName("§8|§l§aEmerald§r§8|§a " . $player->getName());
            $player->setNameTag("§a " . $player->getName());
            $this->att($player)->setPermission("vip.rmc", true, "vip.rmc");
            $this->att($player)->setPermission("mvp.rmc", true, "mvp.rmc");
            $this->att($player)->unsetPermission("staff.rmc");
            $this->att($player)->unsetPermission("kill.rmc");
            $this->att($player)->unsetPermission("owner.rmc");
            $this->att($player)->unsetPermission("staffm.rmc");
            $this->att($player)->unsetPermission("pocketmine.command.gamemode");
            
        }
        if($this->ranksdata->get("Rank") == "Youtuber"){
            $player->setDisplayName("§8|§l§cYou§ftuber§r§8|§c " . $player->getName());
            $player->setNameTag("§8|§l§cYou§ftuber§r§8|§c " . $player->getName());
            $this->att($player)->setPermission("vip.rmc", true, "vip.rmc");
            $this->att($player)->setPermission("mvp.rmc", true, "mvp.rmc");
            $this->att($player)->unsetPermission("staff.rmc");
            $this->att($player)->unsetPermission("kill.rmc");
            $this->att($player)->unsetPermission("owner.rmc");
            $this->att($player)->unsetPermission("staffm.rmc");
            
        }
        if($this->ranksdata->get("Rank") == "Mod"){
            $player->setDisplayName("§8|§l§cMod§r§8|§c " . $player->getName());
            $player->setNameTag("§8|§l§cMod§r§8|§c " . $player->getName());
            $this->att($player)->setPermission("vip.rmc", true, "vip.rmc");
            $this->att($player)->setPermission("mvp.rmc", true, "mvp.rmc");
            $this->att($player)->setPermission("staff.rmc", true, "staff.rmc");
            $this->att($player)->setPermission("pocketmine.command.gamemode", true, "pocketmine.command.gamemode");
        }
        if($this->ranksdata->get("Rank") == "Admin"){
            $player->setDisplayName("§8|§l§4Admin§r§8|§c " . $player->getName());
            $player->setNameTag("§8|§l§4Admin§r§8|§c " . $player->getName());
            $this->att($player)->setPermission("vip.rmc", true, "vip.rmc");
            $this->att($player)->setPermission("mvp.rmc", true, "mvp.rmc");
            $this->att($player)->setPermission("staff.rmc", true, "staff.rmc");
            $this->att($player)->setPermission("pocketmine.command.gamemode", true, "pocketmine.command.gamemode");
            
        }
        if($this->ranksdata->get("Rank") == "StaffManager"){
            $player->setDisplayName("§8|§l§aStaffManager§r§8|§c " . $player->getName());
            $player->setNameTag("§8|§l§aStaffManager§r§8|§c " . $player->getName());
            $this->att($player)->setPermission("vip.rmc", true, "vip.rmc");
            $this->att($player)->setPermission("mvp.rmc", true, "mvp.rmc");
            $this->att($player)->setPermission("staff.rmc", true, "staff.rmc");
            $this->att($player)->setPermission("kill.rmc", true, "kill.rmc");
            $this->att($player)->setPermission("pocketmine.command.gamemode", true, "pocketmine.command.gamemode");
            $this->att($player)->unsetPermission("staffm.rmc");
        }
        if($this->ranksdata->get("Rank") == "Manager"){
            $player->setDisplayName("§8|§l§aManager§r§8|§c " . $player->getName());
            $player->setNameTag("§8|§l§aManager§r§8|§c " . $player->getName());
            $this->att($player)->setPermission("vip.rmc", true, "vip.rmc");
            $this->att($player)->setPermission("mvp.rmc", true, "mvp.rmc");
            $this->att($player)->setPermission("staff.rmc", true, "staff.rmc");
            $this->att($player)->setPermission("kill.rmc", true, "kill.rmc");
            $this->att($player)->setPermission("pocketmine.command.gamemode", true, "pocketmine.command.gamemode");
            $this->att($player)->unsetPermission("staffm.rmc");
        }
        if($this->ranksdata->get("Rank") == "CoOwner"){
            $player->setDisplayName("§8|§l§eCo-Owner§r§8|§c " . $player->getName());
            $player->setNameTag("§8|§l§eCo-Owner§r§8|§c " . $player->getName());
            $this->att($player)->setPermission("vip.rmc", true, "vip.rmc");
            $this->att($player)->setPermission("mvp.rmc", true, "mvp.rmc");
            $this->att($player)->setPermission("staff.rmc", true, "staff.rmc");
            $this->att($player)->setPermission("kill.rmc", true, "kill.rmc");
            $this->att($player)->setPermission("owner.rmc", true, "owner.rmc");
            $this->att($player)->setPermission("pocketmine.command.gamemode", true, "pocketmine.command.gamemode");
            $this->att($player)->unsetPermission("staffm.rmc");
        }
        if($this->ranksdata->get("Rank") == "Owner"){
            $player->setDisplayName("§8|§l§4Owner§r§8|§4 " . $player->getName());
            $player->setNameTag("§8|§l§4Owner§r§8|§4 " . $player->getName());
            $this->att($player)->setPermission("vip.rmc", true, "vip.rmc");
            $this->att($player)->setPermission("mvp.rmc", true, "mvp.rmc");
            $this->att($player)->setPermission("staff.rmc", true, "staff.rmc");
            $this->att($player)->setPermission("kill.rmc", true, "kill.rmc");
            $this->att($player)->setPermission("owner.rmc", true, "owner.rmc");
            $this->att($player)->setPermission("pocketmine.command.gamemode", true, "pocketmine.command.gamemode");
            $this->att($player)->unsetPermission("staffm.rmc");
        }
        if($this->ranksdata->get("Ban Status") == "True"){
            $player->kick("§l[§4RMC System§f] >> §cYou Have Been §4Permently §cBanned from \n§r§4RedstoneMc §6Network");
        }
    }
    public function onInteract(PlayerInteractEvent $e){
        $player = $e->getPlayer();
        $item = $e->getPlayer();
        $itemname = $player->getInventory()->getItemInHand()->getName();
        if($item->getId() == 360 || $itemname == "§l§cGames"){
            $e->setCancelled();
            $this->gamesform($player);
            return true;
        }
        if($item->getId() == 152 || $itemname == "§l§bGadgets"){
            $e->setCancelled();
            $this->customizeForm($player);
            return true;
        }
        if($item->getId() == 264 || $itemname == "§l§aProfile"){
            $e->setCancelled();
            $this->profile($player);
            return true;
        }
    }

    public function gamesform($player){
        
        $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $player, int $data = null){
            $result = $data;
            if($result == null){
                return true;
            }
            switch($result){
                case 1:
                    $player->sendMessage("§l[§4RMC System§f] >> §r§4Requesting Teleport to §bKnock GearUP 3.0 ........");
                    $player->getInventory()->clearAll();
                    $player->getArmorInventory()->clearAll();
                    $this->getServer()->loadLevel("knockgear");
                    $player->teleport($this->getServer()->getLevelByName("knockgear")->getSafeSpawn());
                    $player->sendMessage("§l[§4RMC System§f] >> §r§aYou Have Been Teleported To The §bKnock Gear UP v3.0§f");
                    $this->getServer->broadcastMessage("[§a+§f] §a" . $player->getName() . " Has Joined §l§bKnock v3.0");
                break;
                case 2:
                    $player->sendMessage("§l[§4RMC System§f] >> §r§4Requesting Teleport to §cKitPvP v2.0 GearUP §4 ........");
                    $player->getInventory()->clearAll();
                    $player->getArmorInventory()->clearAll();
                    $this->getServer()->loadLevel("kitpvpgear");
                    $player->teleport($this->getServer()->getLevelByName("kitpvpgear")->getSafeSpawn());
                    $player->sendMessage("§l[§4RMC System§f] >> §r§aYou Have Been Teleported To The §cKitPvP v2.0 GearUP §f");
                break;
                case 3:
                    $player->sendMessage("§l[§4RMC System§f] >> §aRequesting Teleport to §9Bow§7Game v2.0 .......");
                    $player->sendMessage("§l[§4RMC System§f] >> §cSorry §9Bow§7Game Server Request Get into Error");
                break;
                case 4:
                    $player->sendMessage("§l[§4RMC System§f] >> §r§aRequesting Teleport to §9Sky§7PvP §l§cBETA........");
                    $player->sendMessage("§l[§4RMC System§f] >> §cSorry SkyPvP Server Request Get into Error");
                break;
                case 5:
                    $player->sendMessage("§l[§4RMC System§f] >> §r§4Requesting Teleport to §l§7Prison §4........");
                    $player->getInventory()->clearAll();
                    $player->getArmorInventory()->clearAll();
                    $this->getServer()->loadLevel("prison");
                    $player->teleport($this->getServer()->getLevelByName("prison")->getSafeSpawn());
                    $player->sendMessage("§l[§4RMC System§f] >> §r§aYou Have Been Teleported To The §c7Prison§f");
                break;
            }
        });
        $form->setTitle("§cGames");
        $form->addButton("§cExit From Selection");
        $form->addButton("§l§bKnock v2.0");
        $form->addButton("§l§cKitPvP");
        $form->addButton("§l§cBow §7Game\n§r§cMaintanence");
        $form->addButton("§l§9Sky§7PvP\n§r§cMaintanence");
        $form->addButton("§l§7Prison");
        $form->sendToPlayer($player);
        return $form;
    }
    public function customizeForm($player){
        $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $player, int $data = null){
            $result = $data;
            if($result == null){
                return true;
            }
            switch($result){
                case 1:
                    if($player->hasPermission("vip.rmc")){
                        $this->particles($player);
                    } else{
                        $player->sendMessage("§l[§4RMC System§f] >> §cSorry You Should Have A §6Heigher Rank§c To Use That");
                    }
                break;
                case 2:
                    if($player->hasPermission("vip.rmc")){
                        $this->boots($player);   
                    }else{
                        $player->sendMessage("§l[§4RMC System§f] >> §cSorry You Should Have A §6Heigher Rank§c To Use That");
                    } 
                break;
                case 3:
                    if($player->hasPermission("vip.rmc")){
                        $player->sendMessage("§l[§4RMC System§f] >> §cWe are Sorry this is Under Coding - RedstoneMc Studios 2020 - 2021");
                    } 
                break;
            }

        });
        $form->setTitle("§cCustomize");
        $form->addButton("§4EXIT");
        $form->addButton("• §aParticles");
        $form->addButton("• §9Shoes");
        $form->addButton("• §7PlaySound");
        $form->sendToPlayer($player);
        return $form;
    }
    public function onMove(PlayerMoveEvent $e){
        $player = $e->getPlayer();
        $level = $player->getLevel();
        $levename = $player->getLevel()->getName();
        $particle = $e->getPlayer();
        $x = $player->getX();
        $y = $player->getY();
        $z = $player->getZ();
        $center = new Vector3($x, $y+1.5, $z); 
        if(isset($this->heart[$player->getName()]))  {
            $particleone = $player->getLevel()->addParticle(new HeartParticle($center));
        }
        if(isset($this->flame[$player->getName()]))  {
            $particletwo = $player->getLevel()->addParticle(new FlameParticle($center));
        }
        if(isset($this->smoke[$player->getName()]))  {
            $particlethree = $player->getLevel()->addParticle(new SmokeParticle($center));
        }
        if(isset($this->portal[$player->getName()]))  {
            $particlefour = $player->getLevel()->addParticle(new PortalParticle($center));
        }
        

    }
    /* This Code is Commented Because It Don't Work.. iAbdo 25/5/2021
    public function removeParticles($particleone){
        $particleone->setInvisible(true);
        $player->getLevel()->addParticle($particleone);
    } */
    public function particles($player){
        $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $player, int $data = null){
            $result = $data;
            if($result == null){
                return true;
            }
            switch($result){
                case 1:
                    if(!isset($this->heart[$player->getName()])){
                        $this->heart[$player->getName()] = $player->getName(); 
                    } else{
                        unset($this->heart[$player->getName()]);
                    }
                    #$player->getLevel()->addParticle(new HeartParticle($player->asVector3()));
                break;
                case 2:
                    if(!isset($this->flame[$player->getName()])){
                        $this->flame[$player->getName()] = $player->getName();
                    } else{
                        unset($this->flame[$player->getName()]);
                    }
                break;
                case 3:
                    if(!isset($this->smoke[$player->getName()])){
                        $this->smoke[$player->getName()] = $player->getName();
                    } else{
                        unset($this->smoke[$player->getName()]);
                    }  
                break;
                case 4:
                    if(!isset($this->portal[$player->getName()])){
                        $this->portal[$player->getName()] = $player->getName();
                    } else{
                        unset($this->portal[$player->getName()]);
                    }  
                break;
                case 5:
                    unset($this->portal[$player->getName()]);
                    unset($this->smoke[$player->getName()]);
                    unset($this->flame[$player->getName()]);
                    unset($this->heart[$player->getName()]);
            }
        });
        $form->setTitle("§l§cParticles §fUI");
        $form->addButton("§4EXIT");
        $form->addButton("• §cHearts");
        $form->addButton("• §aFlame");
        $form->addButton("• §8Smoke");
        $form->addButton("• §5Portal");
        $form->addButton("• §cReset");
        $form->sendToPlayer($player);
        return $form;
    }
    public function boots($player){
        $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $player, int $data = null){
            $result = $data;
            if($result = null){
                return true;
            }
            switch($result){
                case 1:
                    $boots = Item::get(309, 0, 1);
                    $player->getArmorInventory()->setBoots($boots);
                    $player->addTitle("§aDone..\n §cYour shoes Has Been Set to §7Silver");
                break;
                case 2:
                    $boots = Item::get(313, 0, 1);
                    $player->getArmorInventory()->setBoots($boots);
                    $player->addTitle("§aDone..\n §cYour shoes Has Been Set to §7Diamond");
                break;
                
            }
        });
        $form->setTitle("§cBoots");
        $form->addButton("§cEXIT");
        $form->addButton("§7Silver");
        $form->addButton("§bDiamond");
        $form->sendToPlayer($player);
        return $form;

    }
    public function onQuit(PlayerQuitEvent $e){
        $player = $e->getPlayer();
        $this->getServer()->broadcastMessage("[§4-§f] " . $player->getName() . " Quitted the Server");
    }
    public function flyform($player){
        $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $player, int $data = null){
            $result = $data;
            if($result == null){
                return true;
            }
            switch($result){
                case 1:
                    $player->setAllowFlight(true);
                    $player->sendMessage("§l[§4RMC System§f] >> §aEnabled Fly Mode For " . $player->getName());
                    $player->getLevel()->addSound(new AnvilFallSound($player));
                break;
                case 2:
                    $player->setAllowFlight(false);
                    $player->sendMessage("§l[§4RMC System§f] >> §4Disabled Fly Mode For " . $player->getName());
                    $player->getLevel()->addSound(new EndermanTeleportSound($player));
                break;
                case 3:
                    $player->setInvisible(true);
                    $player->sendMessage("§l[§4RMC System§f] >> §aEnabled Vanish Mode No One Can See You " . $player->getName());
                    $this->getServer()->broadcastMessage("[§4-§f] " . $player->getName());
                    $player->getLevel()->addSound(new AnvilFallSound($player));
                break;
                case 4:
                    $player->setInvisible(false);
                    $player->sendMessage("§l[§4RMC System§f] >> §4Disabled Vanish Mode Every One Can See You " . $player->getName());
                    $this->getServer()->broadcastMessage("[§a+§f] " . $player->getName());
                    $player->getLevel()->addSound(new EndermanTeleportSound($player));
                break;
            }
        });
        $form->setTitle("§4RedstoneMc§f StaffMode");
        $form->addButton("§4Exit Staff UI§f");
        $form->addButton("§l§aEnable Fly§f");
        $form->addButton("§l§4Disable Fly§f");
        $form->addButton("§l§aEnable Vanish§f");
        $form->addButton("§l§4Disable Vanish§f");
        $form->sendToPlayer($player);
        return $form;
    }
    public function onChat(PlayerChatEvent $e){
        $player = $e->getPlayer();
        $this->ranksdata = new Config($this->getDataFolder() . "players/" . strtolower($player->getName()), Config::YAML, array(
            "Player Name" => $player->getName(),
            "Mute Status" => "False"
        ));
        if($this->ranksdata->get("Mute Status") == "True"){
            $e->setCancelled();
            $player->sendMessage("§l[§4RMC System§f] >> §r§4RedstoneMc §6Network\n     §cAdminstaration\n §aYou are Muted Permently, Thinks its AI Wrong appeal at our facebook Page : RedstoneMc Network");
        }
        if($this->ranksdata->get("Mute Status") == "False"){
            return true;
        }
    }
    public function AntiGreifSystem(BlockBreakEvent $e){
        $player = $e->getPlayer();
        if($player->hasPermission("staff.rmc")){
            return true;
        } else {
            $e->setCancelled();
        }
    }
    public function setSkin($player, string $file, string $ex, string $geo) {
        $skin = $player->getSkin();
        $path = $this->getDataFolder() . $file . $ex;
        $img = @imagecreatefrompng($path);
        $skinbytes = "";
        $s = (int)@getimagesize($path)[1];

        for($y = 0; $y < $s; $y++) {
            for($x = 0; $x < 64; $x++) {
                $colorat = @imagecolorat($img, $x, $y);
                $a = ((~((int)($colorat >> 24))) << 1) & 0xff;
                $r = ($colorat >> 16) & 0xff;
                $g = ($colorat >> 8) & 0xff;
                $b = $colorat & 0xff;
                $skinbytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }

        @imagedestroy($img);

        $player->setSkin(new Skin($skin->getSkinId(), $skinbytes, "", "geometry.." . $geo, file_get_contents($this->getDataFolder() . "steve.geo.json")));
        $player->sendSkin();
        
    }
    
    public function profile($player){
        $this->ranksdata = new Config($this->getDataFolder() . "players/" . strtolower($player->getName()), Config::YAML, array(
            "Player Name" => $player->getName(),
            "Rank" => "Member",
        ));
        $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $player, int $data = null){
            $result = $data;
            if($result = $data){
                return true;
            }

            switch($result){
                
            }
        });

        $form->setTitle("§cYour Profile");
        $form->setContent("§aYour Name :- " . $player->getName());
        $form->setContent("§aYour Rank :- " . $this->ranksdata->get("Rank"));
        $form->setContent("§cRules :- use Command /rules");
        $form->sendToPlayer($player);
        return $form;
    }
    private function att(Player $player){
        if(!isset($this->att[$player->getId()])){
            return $this->att[$player->getId()] = $player->addAttachment($this);
        }
        return $this->att[$player->getId()];
    }

}









// Final Two Line Of Code 
// to be the 1000 Code line
