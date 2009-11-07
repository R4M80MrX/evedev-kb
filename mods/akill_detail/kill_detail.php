<?php
/*
 * Based on work by unknown, Sapyx, Rostik, Tribalize, Ben Thomas, KE and Kovell
 */
require_once('common/includes/class.kill.php');

$page = new Page('Kill details');

$kll_id = intval($_GET['kll_id']);
$kll_external_id = intval($_GET['kll_external_id']);
if(isset($_GET['nolimit'])) $nolimit = true;
else $nolimit = false;
if (!$kll_id && !$kll_external_id)
{
	$html = "No kill id specified.";
	$page->setContent($html);
	$page->generate($html);
	exit;
}

if($kll_id)
{
	$kill = new Kill($kll_id);
}
else
{
	$kill = new Kill($kll_external_id, true);
	$kll_id = $kill->getID();
}
$kill->setDetailedInvolved();

if (!$kill->exists())
{
	$html="That kill doesn't exist.";
	$page->setContent($html);
	$page->generate($html);
	exit;
}

// If a comment is being posted then we don't exit this block.
if(isset($_POST['comment']) && config::get('comments'))
{
	include('common/comments.php');
}

require_once('common/includes/class.killsummarytable.php');
require_once('common/includes/class.pilot.php');
require_once('common/includes/class.corp.php');
require_once('common/includes/class.alliance.php');

$page=new Page('Kill details');
$page->addHeader('<link rel="stylesheet" type="text/css" href="mods/akill_detail/style.css" />');

// check apoc settings are set up. If not, put defaults in.
if(!config::get('apocfitting_colour'))
{
	config::set('apocfitting_colour', 'apoc');
	config::set('apocfitting_themedir', 'panel');
}

$smarty->assign('panel_colour', config::get('apocfitting_colour'));
$smarty->assign('dropped_colour', config::get('apocfitting_dropped_colour'));
$smarty->assign('themedir', config::get('apocfitting_themedir'));
$smarty->assign('showammo', config::get('apocfitting_showammo'));
$smarty->assign('showiskd', config::get('apocfitting_showiskd'));
$smarty->assign('showext', config::get('apocfitting_showext'));
$mapmod = config::get('apocfitting_mapmod');
$sidemap = config::get('apocfitting_sidemap');
$showbox = config::get('apocfitting_showbox');

if (config::get('item_values'))
{
	$smarty->assign('item_values', 'true');

	if ($page->isAdmin())
	{
		if (isset($_POST['submit']) && $_POST['submit'] == 'UpdateValue')
		{
		// Send new value for item to the database
			$qry = new DBQuery();
			$qry->autocommit(false);
			if(isset($_POST['SID']))
			{
				$SID = intval($_POST['SID']);
				$Val = preg_replace('/[^0-9]/','',$_POST[$SID]);
				$qry->execute("INSERT INTO kb3_item_price (typeID, price) VALUES ('".$SID."', '".$Val."') ON DUPLICATE KEY UPDATE price = '".$Val."'");
				$victimship = $kill->getVictimShip();
				$kill->setVictimShip(new Ship($victimship->getID() ));
			}
			else
			{
				$IID = intval($_POST['IID']);
				$Val = preg_replace('/[^0-9]/','',$_POST[$IID]);
				$qry->execute("INSERT INTO kb3_item_price (typeID, price) VALUES ('".$IID."', '".$Val."') ON DUPLICATE KEY UPDATE price = '".$Val."'");
				foreach($kill->destroyeditems_ as $i => $ditem)
				{
					$item = $ditem->getItem();
					if($item->getID() == $IID) $kill->destroyeditems_[$i]->value = $Val;
				}
				foreach($kill->droppeditems_ as $i=> $ditem)
				{
					$item = $ditem->getItem();
					if($item->getID() == $IID) $kill->droppeditems_[$i]->value = $Val;
				}
			}
			$qry->execute("UPDATE kb3_kills SET kll_isk_loss = ".$kill->calculateISKLoss()." WHERE kll_id = ".$kill->getID());
			$qry->autocommit(true);
		}
	}
}

if ($page->isAdmin())
{
	if (isset($_GET['view']) && $_GET['view'] == 'FixSlot')
	{
		$smarty->assign('fixSlot', 'true');
	}

	$smarty->assign('admin', 'true');

	if (isset($_POST['submit']) && $_POST['submit'] == 'UpdateSlot')
	{
		$IID  =$_POST['IID'];
		$KID  =$_POST['KID'];
		$Val  =$_POST[$IID];
		$table=$_POST['TYPE'];
		$old  =$_POST['OLDSLOT'];
		$qry  =new DBQuery();
		$qry->execute("UPDATE kb3_items_" . $table . " SET itd_itl_id ='" . $Val . "' WHERE itd_itm_id=" . $IID
			. " AND itd_kll_id = " . $KID . " AND itd_itl_id = " . $old);
	}
}

// Begin -- Sapyx 25-08-2008

$qry=new DBQuery();

if (ALLIANCE_ID <> '0')
{
	$qry->execute("SELECT all_name FROM kb3_alliances WHERE all_id = " . ALLIANCE_ID) or die($qry->getErrorMsg());

	$row=$qry->getRow();
	$smarty->assign('IsAlly', true);
	$smarty->assign('HomeName', $row['all_name']);

/*
    $qry->execute("SELECT `kb3_corps`.`crp_name` FROM kb3_corps WHERE (kb3_corps.crp_all_id=" . ALLIANCE_ID . ")")
        or die($qry->getErrorMsg());

    $rosso=0x70;
    $verde=0;
    $blu  =0x70;

    while ($row=$qry->getRow())
        {
        $verde+=10;
        $blu-=10;
        $rosso-=15;
        $corps[$row['crp_name']]="RGB($rosso,$verde,$blu)";
        }
 */
}
else
{
	$qry->execute("SELECT crp_name FROM kb3_corps WHERE crp_id = " . CORP_ID) or die($qry->getErrorMsg());

	$row=$qry->getRow();
	$smarty->assign('IsAlly', false);
	$smarty->assign('HomeName', $row['crp_name']);
}

// END --

// victim $smarty->assign('',);
$smarty->assign('KillId', $kill->getID());
$smarty->assign('VictimPortrait', $kill->getVictimPortrait(64));
$smarty->assign('VictimURL', "?a=pilot_detail&amp;plt_id=" . $kill->getVictimID());
$smarty->assign('VictimName', $kill->getVictimName());
$smarty->assign('VictimCorpURL', "?a=corp_detail&amp;crp_id=" . $kill->getVictimCorpID());
$smarty->assign('VictimCorpName', $kill->getVictimCorpName());
$smarty->assign('VictimAllianceURL', "?a=alliance_detail&amp;all_id=" . $kill->getVictimAllianceID());
$smarty->assign('VictimAllianceName', $kill->getVictimAllianceName());
$smarty->assign('VictimDamageTaken', $kill->VictimDamageTaken);
$smarty->assign('InvolvedPartyCount', $kill->getInvolvedPartyCount()); // Anne Sapyx 07/05/2008
$smarty->assign('AllyCorps', $corps);

// involved
$i=1;

$involved=array();
$ownKill= false;
/*
$handle=@fopen("mods/akill_detail/ItemsList", "r");
$itemsArray = array();
if ($handle)
{
	if (!feof($handle))
	{
		$itemsArray[] = explode(", ", fgets($handle));
	}
	fclose($handle);
}
*/
foreach ($kill->involvedparties_ as $inv)
{
	if($i > 10 && !$nolimit)
	{
		$smarty->assign('limited', true);
		$smarty->assign('moreInvolved', count($kill->involvedparties_) - 10);
		$smarty->assign('unlimitURL', '?'.$_SERVER['QUERY_STRING'].'&amp;nolimit');
		break;
	}

	$pilot                     =$inv->getPilot();
	$corp                      =$inv->getCorp();
	$alliance                  =$inv->getAlliance();
	$ship                      =$inv->getShip();
	$weapon                    =$inv->getWeapon();

	$involved[$i]['shipImage'] =$ship->getImage(64);
	$involved[$i]['PilotURL']  ="?a=pilot_detail&amp;plt_id=" . $pilot->getID();
	$involved[$i]['PilotName'] =$pilot->getName();
	$involved[$i]['CorpURL']   ="?a=corp_detail&amp;crp_id=" . $corp->getID();
	$involved[$i]['CorpName']  =$corp->getName();
	$involved[$i]['AlliURL']   ="?a=alliance_detail&amp;all_id=" . $alliance->getID();
	$involved[$i]['AlliName']  =$alliance->getName();
	$involved[$i]['ShipName']  =$ship->getName();
	$involved[$i]['ShipID']    =$ship->externalid_;
	$involved[$i]['damageDone']=$inv->dmgdone_;
	$shipclass                 =$ship->getClass();

	$involved[$i]['shipClass'] =$shipclass->getName();

	if ($pilot->getID() == $kill->getFBPilotID())
	{
		$involved[$i]['FB']="true";
	}
	else
	{
		$involved[$i]['FB']="false";
	}

	if ($pilot->getName() == $weapon->getName())
	{
		$involved[$i]['portrait'] = $corp->getPortraitURL(64);

		if(!file_exists("img/ships/64_64/".$weapon->getID().".png"))
			$involved[$i]['shipImage'] = $involved[$i]['portrait'];
		else 
			$involved[$i]['shipImage'] = IMG_URL."/ships/64_64/".$weapon->getID().".png";
	}
	else
	{
		$involved[$i]['portrait']=$pilot->getPortraitURL(64);
	}

	if ($weapon->getName() != "Unknown" && $weapon->getName() != $ship->getName())
	{
		$involved[$i]['weaponName']=$weapon->getName();
		$involved[$i]['weaponID']  =$weapon->row_['itm_externalid'];
	}
	else
		$involved[$i]['weaponName']="Unknown";

	// Sapyx 16/09/2008 -- from Rostik idea
	// Update by Rostik 19/08/2008 - Add Alliance logo
	//sapyx 29/10/2008
	// Sapyx 16/09/2008 -- from Rostik idea
	// Update by Rostik 19/08/2008 - Add Alliance logo
	//sapyx 29/10/2008
	// Rostik 11/03/2009

	//$handle=@fopen("mods/akill_detail/ItemsList", "r");
/*
	if ($handle)
	{
		//while (!feof($handle))
		foreach($itemsArray as $NPCWeapon)
		{
			//$ItemsArray = explode(",", fgets($handle));
			if (strpos($involved[$i]['weaponName'], $NPCWeapon[0]) !== FALSE)
			{
				$involved[$i]['shipImage']="img/ships/64_64/" . $NPCWeapon[1] . ".png";

				if (!$corp->isNPCCorp())
				{
					if ($involved[$i]['CorpName'] == "None")
						$involved[$i]['portrait']=$alliance->getPortraitURL(128);
					else
						$involved[$i]['portrait']=$corp->getPortraitURL(128);
				}
			}
		}

		//fclose($handle);
	}
*/
	// END -- Sapyx Rostik & C

	// END -- Sapyx Rostik & C

	// Begin -- Sapyx 25-06-2008
	$InvAllies[$involved[$i]['AlliName']]["quantity"]+=1;
	$InvAllies[$involved[$i]['AlliName']]["corps"][$involved[$i]['CorpName']]+=1;
	//HK    $InvShips[$involved[$i]['ShipName'] ." (" .$involved[$i]['shipClass'] .")"] += 1;
	$InvShips[$involved[$i]['ShipName']] += 1;
	if(ALLIANCE_ID >0 && $alliance->getID()==ALLIANCE_ID) $ownKill=true;
	elseif(CORP_ID >0 && $corp->getID()==CORP_ID) $ownKill=true;
	// End -- Sapyx 25-06-2008

	++$i;
}
if($ownKill) $smarty->assign('kill',true);
else $smarty->assign('kill',false);

$smarty->assign_by_ref('involved', $involved);
$smarty->assign_by_ref('AlliesCount', count($InvAllies));
$smarty->assign_by_ref('InvAllies', $InvAllies);
$smarty->assign_by_ref('InvShips', $InvShips);

if (config::get('comments'))
{
	include('common/comments.php');
	$smarty->assign('comments', $comment);
}

// ship, ship details
$ship=$kill->getVictimShip();
$shipclass=$ship->getClass();
$system=$kill->getSystem();

$smarty->assign('VictimShip', $kill->getVictimShip());
$smarty->assign('ShipClass', $ship->getClass());
$smarty->assign('ShipImage', $ship->getImage(64));
$smarty->assign('ShipName', $ship->getName());
$smarty->assign('ShipID', $ship->externalid_);
$smarty->assign('ClassName', $shipclass->getName());
if($page->isAdmin()) $smarty->assign('Ship', $ship);

include_once('common/includes/class.dogma.php');

$ssc=new dogma($ship->externalid_);

$smarty->assign_by_ref('ssc', $ssc);

if ($kill->isClassified())
{
//Admin is able to see classified Systems
	if ($page->isAdmin())
	{
		if ($mapmod)
		{
			$smarty->assign('SystemID', $system->getID());
		}
		$smarty->assign('System', $system->getName() . ' (Classified)');
		$smarty->assign('SystemURL', "?a=system_detail&amp;sys_id=" . $system->getID());
		$smarty->assign('SystemSecurity', $system->getSecurity(true));
	}
	else
	{
		$smarty->assign('System', 'Classified');
		$smarty->assign('SystemURL', "");
		$smarty->assign('SystemSecurity', '0.0');
	}
}
else
{
	if ($mapmod)
	{
		$smarty->assign('SystemID', $system->getID());
	}
	$smarty->assign('System', $system->getName());
	$smarty->assign('SystemURL', "?a=system_detail&amp;sys_id=" . $system->getID());
	$smarty->assign('SystemSecurity', $system->getSecurity(true));
}

$smarty->assign('TimeStamp', $kill->getTimeStamp());
$smarty->assign('VictimShipImg', $ship->getImage(64));

if ($mapmod)
{
	if(strpos($config->get('mods_active'), 'map_mod') !== false)
	{

		$smarty->assign('loc_active', TRUE);
	}
	else
	{
		$smarty->assign('loc_active', FALSE);
	}
}

// preparing slot layout

$slot_array=array();

$slot_array[1]=array
	(
	'img'   => 'icon08_11.png',
	'text'  => 'Fitted - High slot',
	'items' => array()
);

$slot_array[2]=array
	(
	'img'   => 'icon08_10.png',
	'text'  => 'Fitted - Mid slot',
	'items' => array()
);

$slot_array[3]=array
	(
	'img'   => 'icon08_09.png',
	'text'  => 'Fitted - Low slot',
	'items' => array()
);

$slot_array[5]=array
	(
	'img'   => 'icon68_01.png',
	'text'  => 'Fitted - Rig slot',
	'items' => array()
);

$slot_array[6]=array
	(
	'img'   => 'icon02_10.png',
	'text'  => 'Drone bay',
	'items' => array()
);

$slot_array[4]=array
	(
	'img'   => 'icon03_14.png',
	'text'  => 'Cargo Bay',
	'items' => array()
);
$slot_array[7]=array
	(
	'img'   => 'icon76_04.png',
	'text'  => 'Fitted - Subsystems',
	'items' => array()
);

// ship fitting
if (count($kill->destroyeditems_) > 0)
{
	$dest_array=array();

	foreach ($kill->destroyeditems_ as $destroyed)
	{
		$item = $destroyed->getItem();
		$i_qty=$destroyed->getQuantity();

		if (config::get('item_values'))
		{
			$value=$destroyed->getValue();
			$TotalValue+=$value * $i_qty;
			$formatted=$destroyed->getFormattedValue();
		}

		$i_name     =$item->getName();
		$i_location =$destroyed->getLocationID();
		$i_id       =$item->getID();
		$i_usedgroup=$item->get_used_launcher_group();

		$dest_array[$i_location][]=array
			(
			'Icon'        => $item->getIcon(32),
			'Name'        => $i_name,
			'Quantity'    => $i_qty,
			'Value'       => $formatted,
			'single_unit' => $value,
			'itemID'      => $i_id,
			'slotID'      => $i_location
		);

		//Fitting, KE - add destroyed items to an array of all fitted items.
		if (($i_location != 4) && ($i_location != 5) && ($i_location != 6))
		{
			if (($i_usedgroup != 0))
			{
				if ($i_location == 1)
				{
					$i_ammo=$item->get_ammo_size($i_name);

					if ($i_usedgroup == 481)
					{
						$i_ammo=0;
					}
				}
				else
				{
					$i_ammo=0;
				}

				$ammo_array[$i_location][]=array
					(
					'Name'        => $i_name,
					'Icon'        => $item->getIcon(24),
					'itemID'      => $i_id,
					'usedgroupID' => $i_usedgroup,
					'size'        => $i_ammo
				);
			}
			else
			{
				for ($count=0; $count < $i_qty; $count++)
				{
					if ($i_location == 1)
					{
						$i_charge=$item->get_used_charge_size();
					}
					else
					{
						$i_charge=0;
					}

					$fitting_array[$i_location][]=array
						(
						'Name'       => $i_name,
						'Icon'       => $item->getIcon(32),
						'itemID'     => $i_id,
						'groupID'    => $item->get_group_id(),
						'chargeSize' => $i_charge
					);
				}
			}
		}
		else if (($destroyed->getLocationID() == 5))
			{
				for ($count=0; $count < $i_qty; $count++)
				{
					$fitting_array[$i_location][]=array
						(
						'Name'   => $i_name,
						'Icon'   => $item->getIcon(32),
						'itemID' => $i_id
					);
				}
			}
	//fitting thing end
	}
}

if (count($kill->droppeditems_) > 0)
{
	$drop_array=array();

	foreach ($kill->droppeditems_ as $dropped)
	{
		$item = $dropped->getItem();
		$i_qty=$dropped->getQuantity();

		if (config::get('item_values'))
		{
			$value=$dropped->getValue();
			$dropvalue+=$value * $i_qty;
			$formatted=$dropped->getFormattedValue();
		}

		$i_name     =$item->getName();
		$i_location =$dropped->getLocationID();
		$i_id       =$item->getID();
		$i_usedgroup=$item->get_used_launcher_group();

		$drop_array[$i_location][]=array
			(
			'Icon'        => $item->getIcon(32),
			'Name'        => $i_name,
			'Quantity'    => $i_qty,
			'Value'       => $formatted,
			'single_unit' => $value,
			'itemID'      => $i_id,
			'slotID'      => $i_location
		);

		//Fitting -KE, add dropped items to the list
		if (($i_location != 4) && ($i_location != 6))
		{
			if (($i_usedgroup != 0))
			{
				if ($i_location == 1)
				{
					$i_ammo=$item->get_ammo_size($i_name);

					if ($i_usedgroup == 481)
					{
						$i_ammo=0;
					}
				}
				else
				{
					$i_ammo=0;
				}

				$ammo_array[$i_location][]=array
					(
					'Name'        => $i_name,
					'Icon'        => $item->getIcon(24),
					'itemID'      => $i_id,
					'usedgroupID' => $i_usedgroup,
					'size'        => $i_ammo
				);
			}
			else
			{
				for ($count=0; $count < $i_qty; $count++)
				{
					if ($i_location == 1)
					{
						$i_charge=$item->get_used_charge_size();
					}
					else
					{
						$i_charge=0;
					}

					$fitting_array[$i_location][]=array
						(
						'Name'       => $i_name,
						'Icon'       => $item->getIcon(32),
						'itemID'     => $i_id,
						'groupID'    => $item->get_group_id(),
						'chargeSize' => $i_charge
					);
				}
			}
		}
	//fitting thing end

	}
}

//Fitting - KE, sort the fitted items into groupID order, so that several of the same item apear next to each other.
if (is_array($fitting_array[1]))
{
	foreach ($fitting_array[1] as $array_rowh)
	{
		$sort_by_nameh["groupID"][]=$array_rowh["groupID"];
	}

	array_multisort($sort_by_nameh["groupID"], SORT_ASC, $fitting_array[1]);
}

if (is_array($fitting_array[2]))
{
	foreach ($fitting_array[2] as $array_rowm)
	{
		$sort_by_namem["groupID"][]=$array_rowm["groupID"];
	}

	array_multisort($sort_by_namem["groupID"], SORT_ASC, $fitting_array[2]);
}

if (is_array($fitting_array[3]))
{
	foreach ($fitting_array[3] as $array_rowl)
	{
		$sort_by_namel["groupID"][]=$array_rowl["Name"];
	}

	array_multisort($sort_by_namel["groupID"], SORT_ASC, $fitting_array[3]);
}

if (is_array($fitting_array[5]))
{
	foreach ($fitting_array[5] as $array_rowr)
	{
		$sort_by_namer["Name"][]=$array_rowr["Name"];
	}

	array_multisort($sort_by_namer["Name"], SORT_ASC, $fitting_array[5]);
}

if (is_array($fitting_array[7]))
{
	foreach ($fitting_array[7] as $array_rowr)
	{
		$sort_by_namer["groupID"][]=$array_rowr["groupID"];
	}

	array_multisort($sort_by_namer["groupID"], SORT_ASC, $fitting_array[7]);
}

//Fitting - KE, sort the fitted items into name order, so that several of the same item apear next to each other. -end

$length=count($ammo_array[1]);

$temp=array();

if (is_array($fitting_array[1]))
{
	$hiammo=array();

	foreach ($fitting_array[1] as $highfit)
	{
		$group = $highfit["groupID"];
		$size  =$highfit["chargeSize"];

		if ($group
			== 483                          // Modulated Deep Core Miner II, Modulated Strip Miner II and Modulated Deep Core Strip Miner II
			|| $group == 53                     // Laser Turrets
			|| $group == 55                     // Projectile Turrets
			|| $group == 74                     // Hybrid Turrets
			|| ($group >= 506 && $group <= 511) // Some Missile Lauchers
			|| $group == 481                    // Probe Launchers
			|| $group == 899                    // Warp Disruption Field Generator I
			|| $group == 771                    // Heavy Assault Missile Launchers
			|| $group == 589                    // Interdiction Sphere Lauchers
			|| $group == 524                    // Citadel Torpedo Launchers
		)
		{
			$found=0;

			if ($group == 511)
			{
				$group=509;
			} // Assault Missile Lauchers uses same ammo as Standard Missile Lauchers

			if (is_array($ammo_array[1]))
			{
				$i=0;

				while (!($found) && $i < $length)
				{
					$temp = array_shift($ammo_array[1]);

					if (($temp["usedgroupID"] == $group) && ($temp["size"] == $size))
					{
						$hiammo[]=array
							(
							'show' => $smarty->fetch(get_tpl('ammo')),
							'type' => $temp["Icon"]
						);

						$found=1;
					}

					array_push($ammo_array[1], $temp);
					$i++;
				}
			}

			if (!($found))
			{
				$hiammo[]=array
					(
					'show' => $smarty->fetch(get_tpl('ammo')),
					'type' => $smarty->fetch(get_tpl('noicon'))
				);
			}
		}
		else
		{
			$hiammo[]=array
				(
				'show' => $smarty->fetch(get_tpl('blank')),
				'type' => $smarty->fetch(get_tpl('blank'))
			);
		}
	}
}

$length=count($ammo_array[2]);

if (is_array($fitting_array[2]))
{
	$midammo=array();

	foreach ($fitting_array[2] as $midfit)
	{
		$group = $midfit["groupID"];

		if ($group == 76 // Capacitor Boosters
			|| $group == 208 // Remote Sensor Dampeners
			|| $group == 212 // Sensor Boosters
			|| $group == 291 // Tracking Disruptors
			|| $group == 213 // Tracking Computers
			|| $group == 209 // Tracking Links
			|| $group == 290 // Remote Sensor Boosters
		)
		{
			$found=0;

			if (is_array($ammo_array[2]))
			{
				$i=0;

				while (!($found) && $i < $length)
				{
					$temp = array_shift($ammo_array[2]);

					if ($temp["usedgroupID"] == $group)
					{
						$midammo[]=array
							(
							'show' => $smarty->fetch(get_tpl('ammo')),
							'type' => $temp["Icon"]
						);

						$found=1;
					}

					array_push($ammo_array[2], $temp);
					$i++;
				}
			}

			if (!($found))
			{
				$midammo[]=array
					(
					'show' => $smarty->fetch(get_tpl('ammo')),
					'type' => $smarty->fetch(get_tpl('noicon'))
				);
			}
		}
		else
		{
			$midammo[]=array
				(
				'show' => $smarty->fetch(get_tpl('blank')),
				'type' => $smarty->fetch(get_tpl('blank'))
			);
		}
	}
}

if ($TotalValue >= 0)
{
	$Formatted=number_format($TotalValue, 2);
}

// Get Ship Value
$ShipValue=$ship->getPrice();

if (config::get('kd_droptototal'))
{
	$TotalValue+=$dropvalue;
}

$TotalLoss=number_format($TotalValue + $ShipValue, 2);
$ShipValue=number_format($ShipValue, 2);
$dropvalue=number_format($dropvalue, 2);

$smarty->assign_by_ref('destroyed', $dest_array);
$smarty->assign_by_ref('dropped', $drop_array);
$smarty->assign_by_ref('slots', $slot_array);
$smarty->assign_by_ref('fitting_high', $fitting_array[1]);
$smarty->assign_by_ref('fitting_med', $fitting_array[2]);
$smarty->assign_by_ref('fitting_low', $fitting_array[3]);
$smarty->assign_by_ref('fitting_rig', $fitting_array[5]);
$smarty->assign_by_ref('fitting_sub', $fitting_array[7]);
$smarty->assign_by_ref('fitting_ammo_high', $hiammo);
$smarty->assign_by_ref('fitting_ammo_mid', $midammo);
$smarty->assign('ItemValue', $Formatted);
$smarty->assign('DropValue', $dropvalue);
$smarty->assign('ShipValue', $ShipValue);
$smarty->assign('TotalLoss', $TotalLoss);

$hicount =count($fitting_array[1]);
$medcount=count($fitting_array[2]);
$lowcount=count($fitting_array[3]);

$smarty->assign('hic', $hicount);
$smarty->assign('medc', $medcount);
$smarty->assign('lowc', $lowcount);

$menubox=new Box("Menu");
$menubox->setIcon("menu-item.gif");
$menubox->addOption("caption", "View");
$menubox->addOption("link",
	"Killmail",
	"javascript:sndReq('index.php?a=kill_mail&amp;kll_id=" . $kill->getID()
	. "');ReverseContentDisplay('popup')");

if (config::get('apocfitting_showeft'))
{
	$menubox->addOption("link",
		"EFT Fitting",
		"javascript:sndReq('index.php?a=eft_fitting&amp;kll_id=" . $kill->getID()
		. "');ReverseContentDisplay('popup')");
}

if (config::get('apocfitting_showeft2eve'))
{
	$menubox->addOption("link", "EFT To EVE", "javascript:sndReq('index.php?a=convertinkilldetail');ReverseContentDisplay('popup')");
}

if ($kill->relatedKillCount() > 1 || $kill->relatedLossCount() > 1 ||
	((ALLIANCE_ID || CORP_ID || PILOT_ID) && $kill->relatedKillCount() + $kill->relatedLossCount() > 1))
{
	$menubox->addOption("link", "Related kills (" . $kill->relatedKillCount() . "/" . $kill->relatedLossCount() . ")",
		"?a=kill_related&amp;kll_id=" . $kill->getID());
}

if ($page->isAdmin())
{
	$menubox->addOption("caption", "Admin");
	$menubox->addOption("link",
		"Delete",
		"javascript:openWindow('?a=admin_kill_delete&amp;kll_id=" . $kill->getID()
		. "', null, 420, 300, '' );");

	if (isset($_GET['view']) && $_GET['view'] == 'FixSlot')
	{
		$menubox->addOption("link", "Adjust Values", "?a=kill_detail&amp;kll_id=" . $kill->getID() . "");
	}
	else
	{
		$menubox->addOption("link", "Fix Slots", "?a=kill_detail&amp;kll_id=" . $kill->getID() . "&amp;view=FixSlot");
	}
}

$page->addContext($menubox->generate());

if (config::get('kill_points'))
{
	$scorebox=new Box("Points");
	$scorebox->addOption("points", $kill->getKillPoints());
	$page->addContext($scorebox->generate());
}

// Final Blow/Top Damage Dealer - Begin (Ben Thomas - 17/05/09)
if ($showbox)
{
	$topdamage = $involved;

	class FBlowBox
	{

		function generate()
		{
			global $smarty;
			return $smarty->fetch(getcwd().'/mods/akill_detail/damage_box.tpl');
		}
	}

	function multi_sort(&$array, $key)
	{
		usort($array,create_function('$a,$b','if ($a["'.$key.'"] == $b["'.$key.'"]) return 0;' .'return ($a["'.$key.'"] > $b["'.$key.'"]) ? -1 : 1;'));
	}

	multi_sort($topdamage,"damageDone");
	$topdamage = array_slice($topdamage, 0, 1);
	$smarty->assign('topdamage', $topdamage);

	$finalblow = new FBlowBox();
	$page->addContext($finalblow->generate());
}
// Final Blow/Top Damage Dealer - End

//Admin is able to see classsified systems
if ((!$kill->isClassified()) || ($page->isAdmin()))
{
	if (!$sidemap)
	{
		$mapbox=new Box("Map");
		$mapbox->addOption("img", "?a=mapview&amp;sys_id=" . $system->getID() . "&amp;mode=map&amp;size=145");
		$mapbox->addOption("img", "?a=mapview&amp;sys_id=" . $system->getID() . "&amp;mode=region&amp;size=145");
		$mapbox->addOption("img", "?a=mapview&amp;sys_id=" . $system->getID() . "&amp;mode=cons&amp;size=145");
		$page->addContext($mapbox->generate());
	}
}

$html.=$smarty->fetch(getcwd().'/mods/akill_detail/kill_detail.tpl');

$page->setContent($html);
$page->generate();
?>