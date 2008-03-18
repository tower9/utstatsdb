CREATE TABLE %dbpre%items (
  it_num smallint(5) unsigned NOT NULL auto_increment,
  it_type varchar(40) NOT NULL default '',
  it_desc varchar(40) NOT NULL default '',
  it_pickups mediumint(8) unsigned NOT NULL default 0,
  UNIQUE KEY it_num (it_num),
  KEY it_typ (it_type)
) Type=MyISAM;

INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ShieldGunPickup','Shield Gun');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('AssaultRifle','Assault Rifle');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('AssaultRiflePickup','Assault Rifle');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('AssaultAmmoPickup','Assault Rifle Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('AssaultGrenadesPickup','Assault Rifle Grenades');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('BioRifle','Bio Rifle');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('BioRiflePickup','Bio Rifle');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('BioAmmoPickup','Bio Rifle Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ShockRifle','Shock Rifle');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ShockRiflePickup','Shock Rifle');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ShockAmmoPickup','Shock Rifle Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('LinkGun','Link Gun');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('LinkGunPickup','Link Gun');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('LinkAmmoPickup','Link Gun Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('Minigun','Minigun');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('MinigunPickup','Minigun');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('MinigunAmmoPickup','Minigun Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('FlakCannon','Flak Cannon');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('FlakCannonPickup','Flak Cannon');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('FlakAmmoPickup','Flak Cannon Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('RocketLauncher','Rocket Launcher');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('RocketLauncherPickup','Rocket Launcher');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('RocketAmmoPickup','Rocket Launcher Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('SniperRiflePickup','Lightning Gun');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('SniperAmmoPickup','Lightning Gun Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('IonPainterPickup','Ion Painter');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('PainterPickup','Ion Painter');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ONSPainterPickup','Ion Painter');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('RedeemerPickup','Redeemer');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('SuperShockRiflePickup','Super Shock Rifle');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('AdrenelinPickup','Adrenaline');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ShieldPack','Shield Pack');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('LargeShieldPickup','Large Shield Pack');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('HealthVialPickup','Health Vial');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('HealthPack','Health Pack');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('LargeHealthPack','Large Health Pack');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('LargeHealthPickup','Large Health Pack');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('neoMiniHealthPack','Mini Health Pack');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UDamagePickup','Damage Amplifier');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UDamageReward','Damage Amplifier Reward');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ONSAVRiLPickup','AVRiL');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ONSAVRiLAmmoPickup','AVRiL Rockets');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ONSGrenadePickup','Grenade Launcher');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ONSGrenadeAmmoPickup','Grenades');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ONSMineLayerPickup','Mine Layer');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ONSMineAmmoPickup','Mines');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('MiniHealthPack','Health Vial');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('SuperHealthPack','Super Health Pack');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('AdrenalinePickup','Adrenaline');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('WeaponLocker','Weapon Locker');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UDamagePack','Damage Amplifier');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('SuperShieldPack','Super Shield Pack');
#
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ClassicSniperRifle','Classic Sniper Rifle');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ClassicSniperRiflePickup','Classic Sniper Rifle');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ClassicSniperAmmoPickup','Classic Sniper Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ClassicBioRiflePickup','Classic Bio Rifle');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ClassicShockRiflePickup','Classic Shock Rifle');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ClassicMinigunPickup','Classic Minigun');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ClassicFlakCannonPickup','Classic Flak Cannon');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ClassicRocketLauncherPickup','Classic Rocket Launcher');
#
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('Enforcer','Enforcer');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('Double Enforcer','Double Enforcer');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('Chainsaw','Chainsaw');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('GES Bio Rifle','GES Bio Rifle');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('Pulse Gun','Pulse Gun');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('Shock Rifle','ASMD Shock Rifle');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('Ripper','Ripper');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('Flak Cannon','Flak Cannon');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('Rocket Launcher','Rocket Launcher');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('Sniper Rifle','Sniper Rifle');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('Redeemer','Redeemer');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('Large Bullets','Bullets');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('Pulse Cell','Energy Cell');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('Blade Hopper','Razor Blades');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('Shock Core','Shock Core');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('Biosludge Ammo','Tarydium Sludge');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('Flak Shells','Flak Shells');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('RocketPack','Rocket Pack');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('Rifle Round','Rifle Round');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('Box of Rifle Rounds','Box of Rifle Rounds');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('Health Vial','Small Health Vile');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('MedBox','Health Pack');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('LargeMedBox','Large Health Pack');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('Super Health Pack','Big Keg \'O Health');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ThighPads','Thigh Pads');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('Body Armor','Body Armor');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ToxinSuit','Toxin Suit');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ShieldBelt','Shield Belt');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('AntiGrav Boots','AntiGrav Boots');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('Invisibility','Invisibility');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('Damage Amplifier','Damage Amplifier');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('SCUBAGear','Scuba Gear');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('Translocator','Translocator');
#
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('RPGWeaponPickup','Magic Weapon');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('RPGLinkGunPickup','Magic Link Gun');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ArtifactFlightPickup','Boots of Flight');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ArtifactInvulnerabilityPickup','Globe of Invulnerability');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ArtifactLightningRodPickup','Lightning Rod');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ArtifactMonsterSummonPickup','Summoning Charm');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ArtifactTeleportPickup','Teleporter');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ArtifactTripleDamagePickup','Triple Damage');
#
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('RipperPickup','Razorjack');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('BootsOfJumpingPickup','Jump Boots');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('InvisibilityPickup','Invisibility');
#
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('LightArmourPickup','Light Armor');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('MolecularShieldPickup','Molecular Shield');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('HoloClonePickup','Holo Clone');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('SoarPickup','Soar');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('SoarAmmoPickup','Soar Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ParasitePickup','Parasite');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ParasiteAmmoPickup','Parasite Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('CrispePickup','Crispe');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('CrispeAmmoPickup','Crispe Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('CFXPickup','CFX');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('CFXAmmoPickup','CFX Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('FyrianPickup','Fyrian Cannon');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('FyrianAmmoPickup','Fyrian Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('FireChuckerPickup','FireChucker');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('FireChuckerAmmoPickup','FireChucker Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('PepperPotPickup','PepperPot');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('PepperPotAmmoPickup','PepperPot Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('HeliosPickup','Helios');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('HeliosAmmoPickup','Helios Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('PICPickup','Personal Ion Cannon');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('DisturberPickup','Transdimensional Disturber');
#
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('AirblastPickup','Airblast');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('AirblastAmmoPickup','Airblast Fuel');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('BfgPickup','BFG');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('BfgAmmoPickup','BFG Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('qs_pickup','Quicksilver');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('qs_ammopickup','Quicksilver Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('QSGPickup','QSG');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('QSGAmmoPickup','QSG Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('beretta_pickup','Beretta');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('beretta_ammo_pickup','9mm Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('bubblegun_pickup','Bubble Gun');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('bubbleammo_pickup','Bioreactive Gas');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('FleshBombRifle_pickup','FleshBomb Rifle');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('FleshBombRifle_ammo_pickup','FBR darts');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('FreezerPickup','Freezer');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('FreezerAmmoPickup','Freezer Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('HowziePickup','Howitzer Lite');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('HowzieAmmoPickup','Howitzer Shells');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('PhasorPickup','Phasor');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('PhasorAmmoPickup','Phasor Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('railgun_pickup','Railgun');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('railgun_ammo_pickup','Railgun Slugs');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('python_pickup','.357 Magnum Python');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('python_ammo_pickup','.357 Magnum Bullets');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('tomahawk_pickup','Tomahawk');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('tomahawk_ammopickup','Tomahawk Missiles');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('CVTPickup','Compressed Vehicle Transporter');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('CVTAmmoPickup','CVT Pre-paid Card');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('reaper_pickup','Reaper');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('MoverRiflePickup','I.A.M. (Mover)');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('DollBombPickup','Dollbomb');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('HellsawPickup','Hellsaw');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('RiftPadPickup','Rift Pad');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('ConstructionGunPickup','Construction Gun');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('katana_pickup','Katana');
#
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('NewNet_MiniGunPickup','Minigun');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('NewNet_LinkGunPickup','Link Gun');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('NewNet_FlakCannonPickup','Flak Cannon');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('NewNet_SniperRiflePickup','Sniper Rifle');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('NewNet_RocketLauncherPickup','Rocket Launcher');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('NewNet_ShockRiflePickup','Shock Rifle');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('NewNet_BioRiflePickup','Bio Rifle');
#
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('RelicDefenseInventory','Defense Relic');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('RelicStrengthInventory','Strength Relic');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('RelicSpeedInventory','Speed Relic');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('RelicRegenInventory','Regen Relic');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('RelicRedemptionInventory','Redemption Relic');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('RelicDeathInventory','Death Relic');
#
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('XMPEnergyPickup','XMP Engergy');
#
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTWeap_ImpactHammer','Impack Hammer');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTWeap_BioRifle','Bio Rifle');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTWeap_Enforcer','Enforcer');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTWeap_LinkGun','Link Gun');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTWeap_ShockRifle','Shock Rifle');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTWeap_InstagibRifle','InstaGib Rifle');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTWeap_Stinger','Stinger Minigun');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTWeap_FlakCannon','Flak Cannon');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTWeap_RocketLauncher','Rocket Launcher');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTWeap_SniperRifle','Sniper Rifle');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTWeap_Redeemer','Redeemer');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTWeap_Translocator','Translocator');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTWeap_Avril','AVRiL');
#--------------------------------------------------------------------------------------------------
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTAmmo_Enforcer','Enforcer Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTAmmo_BioRifle','Bio Rifle Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTAmmo_BioRifle_Content','Bio Rifle Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTAmmo_LinkGun','Link Gun Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTAmmo_ShockRifle','Shock Rifle Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTAmmo_Stinger','Stinger Minigun Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTAmmo_FlakCannon','Flak Cannon Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTAmmo_RocketLauncher','Rocket Launcher Ammo');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTAmmo_SniperRifle','Sniper Rifle Ammo');
#--------------------------------------------------------------------------------------------------
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('HealthVial','Health Vial');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('MediumHealth','Medium Health Pack');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('SuperHealth','Super Health Pack');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTArmorPickup_Thighpads','Armor Thigh Pads');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTArmorPickup_Vest','Armor Vest');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTArmorPickup_ShieldBelt','Shield Belt');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTDroppedShieldBelt','Used Shield Belt');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTJumpBoots','Jump Boots');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTUDamage','Damage Amplifier');
#--------------------------------------------------------------------------------------------------
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTWeap_Physicsgun','Physics Gun');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTWeaponLocker_Content','Weapon Locker');
#--------------------------------------------------------------------------------------------------
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTWeap_BioRifle_Content','Bio Rifle');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTWeap_Redeemer_Content','Redeemer');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTWeap_Translocator_Content','Translocator');
INSERT INTO %dbpre%items (it_type,it_desc) VALUES('UTWeap_Avril_Content','AVRiL');
#
#INSERT INTO %dbpre%items (it_type,it_desc) VALUES('','');
