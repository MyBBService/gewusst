<?php
if(!defined("IN_MYBB"))
{
	header("HTTP/1.0 404 Not Found");
	exit;
}

if(function_exists("mybbservice_info"))
	define(MODULE, "mybbservice-gewusst");
else
	define(MODULE, "config-gewusst");

$page->add_breadcrumb_item($lang->gewusst, "index.php?module=".MODULE);

if($mybb->input['action'] == "do_add") {
	if(!verify_post_check($mybb->input['my_post_key']))
	{
		flash_message($lang->invalid_post_verify_key2, 'error');
		admin_redirect("index.php?module=".MODULE."&action=add");
	}

	if(!strlen(trim($mybb->input['gewusst'])))
	{
		flash_message($lang->gewusst_not, 'error');
		admin_redirect("index.php?module=".MODULE."&action=add");
	}
	if(!strlen(trim($mybb->input['enable'])))
	{
		flash_message($lang->gewusst_enable_not, 'error');
		admin_redirect("index.php?module=".MODULE."&action=add");
	}

	$insert = array(
		"message" => $db->escape_string($mybb->input['gewusst']),
		"enabled" => (int)$mybb->input['enable']
	);
	$db->insert_query("gewusst", $insert);

	flash_message($lang->gewusst_add_success, 'success');
	admin_redirect("index.php?module=".MODULE."&action=list");
} elseif($mybb->input['action'] == "add") {
	$page->add_breadcrumb_item($lang->gewusst_add, "index.php?module=".MODULE."&action=add");
	$page->output_header($lang->gewusst_add);
	generate_tabs("add");

	$form = new Form("index.php?module=".MODULE."&amp;action=do_add", "post");
	$form_container = new FormContainer($lang->gewusst_add);

	$add_gewusst = $form->generate_text_area("gewusst");
	$form_container->output_row($lang->gewusst." <em>*</em>", $lang->gewusst_desc, $add_gewusst);

	$add_enable = $form->generate_yes_no_radio("enable");
	$form_container->output_row($lang->gewusst_enable." <em>*</em>", '', $add_enable);

	$form_container->end();

	$buttons[] = $form->generate_submit_button($lang->gewusst_submit);
	$buttons[] = $form->generate_reset_button($lang->reset);
	$form->output_submit_wrapper($buttons);
	$form->end();
} elseif($mybb->input['action']=="delete") {
	if(!strlen(trim($mybb->input['id'])))
	{
		flash_message($lang->gewusst_error, 'error');
		admin_redirect("index.php?module=".MODULE);
	}
	$id=(int)$mybb->input['id'];
	$db->delete_query("gewusst", "id='{$id}'");
	flash_message($lang->gewusst_delete_success, 'success');
	admin_redirect("index.php?module=".MODULE);
} elseif($mybb->input['action']=="enable") {
	if(!strlen(trim($mybb->input['id'])))
	{
		flash_message($lang->gewusst_error, 'error');
		admin_redirect("index.php?module=".MODULE);
	}
	$id=(int)$mybb->input['id'];
	$query = $db->simple_select("gewusst", "enabled", "id='{$id}'");
	if($db->num_rows($query) != 1)
	{
		flash_message($lang->gewusst_error, 'error');
		admin_redirect("index.php?module=".MODULE);
	}
	$frage = $db->fetch_array($query);
	if($frage['enabled']) {
		$enabled = 0;
		$lang->gewusst_enable_success=$lang->sprintf($lang->gewusst_enable_success, $lang->gewusst_deactivate);
	} else {
		$enabled = 1;
		$lang->gewusst_enable_success=$lang->sprintf($lang->gewusst_enable_success, $lang->gewusst_activate);
	}
	$db->update_query("gewusst", array("enabled"=>$enabled), "id='{$id}'");
	flash_message($lang->gewusst_enable_success, 'success');
	admin_redirect("index.php?module=".MODULE);
} elseif($mybb->input['action']=="do_edit") {
	if(!strlen(trim($mybb->input['id'])))
	{
		flash_message($lang->gewusst_error, 'error');
		admin_redirect("index.php?module=".MODULE);
	}
	$id=(int)$mybb->input['id'];
	if(!verify_post_check($mybb->input['my_post_key']))
	{
		flash_message($lang->invalid_post_verify_key2, 'error');
		admin_redirect("index.php?module=".MODULE."&action=edit&id=$id");
	}

	if(!strlen(trim($mybb->input['gewusst'])))
	{
		flash_message($lang->gewusst_not, 'error');
		admin_redirect("index.php?module=".MODULE);
	}
	if(!strlen(trim($mybb->input['enable'])))
	{
		flash_message($lang->gewusst_enable_not, 'error');
		admin_redirect("index.php?module=".MODULE);
	}

	$update = array(
		"message" => $db->escape_string($mybb->input['gewusst']),
		"enabled" => (int)$mybb->input['enable']
	);
	$db->update_query("gewusst", $update, "id='{$id}'");
	flash_message($lang->gewusst_edit_success, 'success');
	admin_redirect("index.php?module=".MODULE);
} elseif($mybb->input['action']=="edit") {
	if(!strlen(trim($mybb->input['id'])))
	{
		flash_message($lang->gewusst_error, 'error');
		admin_redirect("index.php?module=".MODULE);
	}
	$id=(int)$mybb->input['id'];
	$query = $db->simple_select("gewusst", "*", "id='{$id}'");
	if($db->num_rows($query) != 1)
	{
		flash_message($lang->gewusst_error, 'error');
		admin_redirect("index.php?module=".MODULE);
	}
	$frage = $db->fetch_array($query);

	$page->add_breadcrumb_item($lang->edit, "index.php?module=".MODULE."&amp;action=edit&amp;id=$id");
	$page->output_header($lang->gewusst);
	generate_tabs("list");

	$form = new Form("index.php?module=".MODULE."&amp;action=do_edit", "post");
	$form_container = new FormContainer($lang->gewusst);

	$add_gewusst = $form->generate_text_area("gewusst", $frage['message']);
	$form_container->output_row($lang->gewusst." <em>*</em>", $lang->gewusst_desc, $add_gewusst);

	$add_enable = $form->generate_yes_no_radio("enable", $frage['enabled']);
	$form_container->output_row($lang->gewusst_enable." <em>*</em>", '', $add_enable);

	echo $form->generate_hidden_field("id", $id);
	$form_container->end();

	$buttons[] = $form->generate_submit_button($lang->gewusst_submit);
	$buttons[] = $form->generate_reset_button($lang->reset);
	$form->output_submit_wrapper($buttons);
	$form->end();
} else {
	$page->output_header($lang->gewusst);
	generate_tabs("list");

	$table = new Table;

	$table->construct_header($lang->gewusst, array("colspan" => 2));
	$table->construct_header($lang->controls, array("colspan" => 2, 'class' => 'align_center'));

	$query = $db->simple_select("gewusst", "*", "", array("order_by"=>"enabled"));
	if($db->num_rows($query) > 0)
	{
		while($frage = $db->fetch_array($query))
		{
			if($frage['enabled']) {
				$icon = "<img src=\"styles/{$page->style}/images/icons/bullet_on.png\" alt=\"(Active)\" title=\"\" /> ";
			} else {
				$icon = "<img src=\"styles/{$page->style}/images/icons/bullet_off.png\" alt=\"(Inactive)\" title=\"\" /> ";
			}
			$table->construct_cell("<a href=\"index.php?module=".MODULE."&amp;action=enable&amp;id={$frage['id']}\">$icon</a>", array('width' => '2%'));
			$table->construct_cell($frage['message']);
			$table->construct_cell("<a href=\"index.php?module=".MODULE."&amp;action=edit&amp;id={$frage['id']}\">{$lang->edit}</a>", array('class' => 'align_center', 'width' => '10%'));
			$table->construct_cell("<a href=\"index.php?module=".MODULE."&amp;action=delete&amp;id={$frage['id']}\">{$lang->delete}</a>", array('class' => 'align_center', 'width' => '10%'));
			$table->construct_row();
		}
	} else {
		$table->construct_cell($lang->gewusst_no, array('class' => 'align_center', 'colspan' => 6));
		$table->construct_row();
	}
	$table->output($lang->gewusst);
}

$page->output_footer();

function generate_tabs($selected)
{
	global $lang, $page;

	$sub_tabs = array();
	$sub_tabs['list'] = array(
		'title' => $lang->gewusst_list,
		'link' => "index.php?module=".MODULE."&amp;action=list",
		'description' => $lang->gewusst_list_desc
	);
	$sub_tabs['add'] = array(
		'title' => $lang->gewusst_add,
		'link' => "index.php?module=".MODULE."&amp;action=add",
		'description' => $lang->gewusst_add_desc
	);

	$page->output_nav_tabs($sub_tabs, $selected);
}
?>