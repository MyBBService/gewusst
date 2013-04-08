<?php
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}


$plugins->add_hook("index_start", "gewusst");
$plugins->add_hook("admin_config_menu", "gewusst_admin_config_menu");
$plugins->add_hook("admin_config_action_handler", "gewusst_admin_config_action_handler");
$plugins->add_hook("admin_config_permissions", "gewusst_admin_config_permissions");

function gewusst_info()
{
	return array(
		"name"			=> "Schon gewusst?",
		"description"	=> "Erstellt eine einfache Anzeige mit nützlichen Infos",
		"website"		=> "http://mybbservice.de/",
		"author"		=> "MyBBService",
		"authorsite"	=> "http://mybbservice.de/",
		"version"		=> "0.1",
		"guid" 			=> "",
		"compatibility" => "16*"
	);
}

function gewusst_install()
{
	global $db;
	$col = $db->build_create_table_collation();
	$db->query("CREATE TABLE `".TABLE_PREFIX."gewusst` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`message` text NOT NULL,
		`enabled` tinyint(1) NOT NULL, 
		PRIMARY KEY (`id`) ) ENGINE=MyISAM {$col}");
}

function gewusst_is_installed()
{
	global $db;
	return $db->table_exists("gewusst");
}

function gewusst_uninstall()
{
	global $db;
	$db->drop_table("gewusst");
}

function gewusst_activate()
{
	require MYBB_ROOT."inc/adminfunctions_templates.php";
	find_replace_templatesets("index", "#".preg_quote('{$header}')."#i", '{$header}{$gewusst}');
}

function gewusst_deactivate()
{
	require MYBB_ROOT."inc/adminfunctions_templates.php";
	find_replace_templatesets("index", "#".preg_quote('{$gewusst}')."#i", "", 0);
}

function gewusst_admin_config_menu($sub_menu)
{
	global $lang;

	$lang->load("gewusst");

	$sub_menu[] = array("id" => "gewusst", "title" => $lang->gewusst, "link" => "index.php?module=config-gewusst");

	return $sub_menu;
}

function gewusst_admin_config_action_handler($actions)
{
	$actions['gewusst'] = array(
		"active" => "gewusst",
		"file" => "gewusst.php"
	);

	return $actions;
}

function gewusst_admin_config_permissions($admin_permissions)
{
	global $lang;

	$lang->load("gewusst");

	$admin_permissions['gewusst'] = $lang->gewusst_permission;

	return $admin_permissions;
}

function gewusst()
{
	global $gewusst, $mybb, $db, $lang;
	$gewusst = "";
	$query=$db->simple_select("gewusst", "*", "enabled='1'", array("order_by"=>"RAND()", "limit"=>1));
	$frage=$db->fetch_array($query);
	$gewusst = "<div style=\"text-align: center; color: #000; margin-bottom: 15px;\">{$frage['message']}</div>";
}
?>