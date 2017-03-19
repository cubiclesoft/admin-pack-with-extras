<?php
	// Admin Pack.
	// (C) 2014 CubicleSoft.  All Rights Reserved.

	// This small package exists to make it easy to design quick-n-dirty administrative backends that look good.
	// This file is well-commented.  When republishing based on this work, copyrights must remain intact.
	// Otherwise, feel free to delete the comments and make the necessary changes to make it work.
	// The obvious shouldn't have to be stated, but you need to know PHP to use this package.

	require_once "support/str_basics.php";
	require_once "support/page_basics.php";
	require_once "support/adminpack_extras.php";

	Str::ProcessAllInput();

	// $bb_randpage is used in combination with a user token to prevent hackers from sending malicious URLs.
	// [Put random content into the string.  Try www.random.org.]
	// https://www.random.org/integers/?num=100&min=0&max=255&col=10&base=16&format=plain&rnd=new
	$bb_randpage = "[Random content goes here]";
	$bb_rootname = "Tool Name";

	// [Put your login management, permissions to access this page, and any obvious initialization logic here.]
	// [If you want, you can make any error messages look nice using the BB_GeneratePage() call.]
	// [$bb_usertoken should be a string that uniquely identifies the user without directly identifying them.  For example, a session ID.]
	$bb_usertoken = "";


	BB_ProcessPageToken("action");

	// Menu/Navigation options.
	$menuopts = array(
		"Temp Title/Section" => array(
			"Some Page" => BB_GetRequestURLBase() . "?action=somepage&sec_t=" . BB_CreateSecurityToken("somepage"),
			"Some Page 2" => BB_GetRequestURLBase() . "?action=somepage2&sec_t=" . BB_CreateSecurityToken("somepage2")
		)
	);

	// Example mobile switcher.
	if (isset($_COOKIE["bb_layout"]) && $_COOKIE["bb_layout"] == "mobile")
	{
		require_once "support/mobile_layout.php";
		$menuopts["Switch To"] = array(
			"Main Layout" => BB_GetRequestURLBase() . "?action=bb_setlayout&layout=&sec_t=" . BB_CreateSecurityToken("bb_setlayout")
		);
	}
	else
	{
		$menuopts["Switch To"] = array(
			"Mobile Layout" => BB_GetRequestURLBase() . "?action=bb_setlayout&layout=mobile&sec_t=" . BB_CreateSecurityToken("bb_setlayout")
		);
	}

	if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "bb_setlayout")
	{
		SetCookieFixDomain("bb_layout", preg_replace('/[^a-z]/', "", $_REQUEST["layout"]), time() + 365 * 24 * 60 * 60);

		BB_RedirectPage("success", "Successfully switched the layout.");
	}
	else if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "somepage")
	{
		if (isset($_REQUEST["field1"]))
		{
			if ($_REQUEST["field1"] == "")  BB_SetPageMessage("error", "Please fill in 'Field 1'.");

			if (BB_GetPageMessageType() != "error")
			{
				// [Save data here.]

				BB_RedirectPage("success", "Successfully saved the data.");
			}
		}

		// [Do processing here to generate content options dynamically.]
		$somevar = "default value 2";

		$items = array("Furries", "Fuzzies", "Fluffies", "Puppies", "Kitties", "Tribbles", "Unicorns");
		$rows = array();
		foreach ($items as $num => $item)
		{
			$rows[] = array(($num + 1), htmlspecialchars($item), "<a href=\"" . BB_GetRequestURLBase() . "?action=somepage_edit&id=" . ($num + 1) . "&sec_t=" . BB_CreateSecurityToken("somepage_edit") . "\">Edit</a>");
		}

		if (file_exists("support/adminpack_calendar_table.php"))  require_once "support/adminpack_calendar_table.php";
		if (file_exists("support/adminpack_chart.php"))  require_once "support/adminpack_chart.php";

		$tomorrow = mktime(0, 0, 0, date("n"), date("j") + 1);

		$contentopts = array(
			"desc" => "This is some page.",
			"nonce" => "action",
			"hidden" => array(
				"action" => "somepage"
			),
			"fields" => array(
				array(
					"title" => "Some Options",
					"type" => "accordion"
				),
				array(
					"title" => "Field 1",
					"type" => "text",
					"name" => "field1",
					"value" => BB_GetValue("field1", ""),
					"desc" => "Description for Field 1."
				),
				array(
					"title" => "Field 2",
					"type" => "text",
					"name" => "field2",
					"value" => BB_GetValue("field2", $somevar),
					"desc" => "Description for Field 2."
				),
				array(
					"title" => "Some More Options",
					"type" => "accordion"
				),
				"startrow",
				array(
					"title" => "Field 2a",
					"type" => "text",
					"width" => "200px",
					"name" => "field2a",
					"default" => "",
				),
				array(
					"title" => "Field 2b",
					"type" => "text",
					"width" => "50px",
					"name" => "field2b",
					"default" => "",
				),
				"endrow",
				array(
					"title" => "Date",
					"type" => "date",
					"name" => "date",
					"default" => date("Y-m-d"),
					"desc" => "Description for Date."
				),
				array(
					"title" => "Table",
					"split" => false,
					"type" => "table",
					"cols" => array("ID", "Type", "Options"),
					"rows" => $rows,
					"order" => "Order",
					"stickyheader" => true,
					"desc" => "Description for Table.  Drag-and-drop and sticky header enabled."
				),
				"nosplit",
				"startrow",
				array(
					"title" => "Field 2c",
					"type" => "text",
					"width" => "200px",
					"name" => "field2c",
					"default" => "",
				),
				array(
					"title" => "Field 2d",
					"type" => "text",
					"width" => "50px",
					"name" => "field2d",
					"default" => "",
				),
				"endrow",
				array(
					"title" => "File",
					"type" => "file",
					"name" => "file",
					"desc" => "Description for File."
				),
				"endaccordion",
				"split",
				"startrow",
				array(
					"title" => "Field 3",
					"type" => "text",
					"width" => "200px",
					"name" => "field3",
					"default" => "default value",
					"desc" => "Description for Field 3."
				),
				array(
					"title" => "Field 4",
					"type" => "text",
					"width" => "200px",
					"name" => "field4",
					"default" => $somevar
				),
				"startrow",
				array(
					"title" => "Field 5",
					"type" => "text",
					"width" => "220px",
					"name" => "field5",
					"default" => "default value",
					"desc" => "Description for Field 5."
				),
				array(
					"title" => "Field 6",
					"type" => "text",
					"width" => "220px",
					"name" => "field6",
					"default" => $somevar,
					"desc" => "Description for Field 6."
				),
				"endrow",
				"split",
				// NOTE:  Dropdown and flat are incompatible and can't be on the same page.
//				array(
//					"title" => "Field 7",
//					"type" => "select",
//					"multiple" => true,
//					"mode" => "dropdown",
//					"height" => "250px",
//					"name" => "field7",
//					"options" => array("name" => "Name", "email" => "E-mail Address", "phone" => "Phone Number"),
//					"select" => BB_SelectValues(BB_GetValue("field7", array())),
//					"desc" => "Description for Field 7."
//				),
				array(
					"title" => "Field 7",
					"type" => "select",
					"multiple" => true,
					"mode" => "flat",
					"height" => "250px",
					"name" => "field7",
					"options" => array("name" => "Name", "email" => "E-mail Address", "phone" => "Phone Number"),
					"select" => BB_SelectValues(BB_GetValue("field7", array())),
					"desc" => "Description for Field 7."
				),
				array(
					"title" => "Field 8",
					"type" => "select",
					"width" => "100%",
					"multiple" => true,
					"mode" => "tags",
					"name" => "field8",
					"options" => array("name" => "Name", "email" => "E-mail Address", "phone" => "Phone Number"),
					"default" => array(),
					"desc" => "Description for Field 8."
				),
				array(
					"title" => "Module:  Calendar Table",
					"type" => "calendar",
					"startyear" => date("Y"),
					"startmonth" => date("m"),
					"endyear" => date("Y"),
					"endmonth" => date("m"),
					"cols" => array("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"),
					"data" => array(date("Y-m-d", $tomorrow) => "<a href=\"https://google.com/?q=" . urlencode("things to do on " . date("l, F j, Y", $tomorrow)) . "\" target=\"_blank\"><b>" . date("j", $tomorrow) . "</b></a>"),
					"desc" => "Description for calendar.  This feature requires Admin Pack Modules to be installed."
				),
				array(
					"title" => "Module:  Chart (Line)",
					"type" => "chart",
					"chart" => "line",
					"data" => array("key1" => array(1, 5, 10, 7, 6, 9, 8), "key2" => array(10, 6, 3, 4, 5, 6, 7)),
					"options" => array("grid.x.show" => true, "zoom.enabled" => true),
					"desc" => "Description for line chart.  This feature requires Admin Pack Modules to be installed."
				),
				array(
					"title" => "Module:  Chart (Pie)",
					"type" => "chart",
					"chart" => "pie",
					"data" => array("key1" => array(1, 5, 10, 7, 6, 9, 8), "key2" => array(10, 6, 3, 4, 5, 6, 7)),
					"desc" => "Description for pie chart.  This feature requires Admin Pack Modules to be installed."
				),
				array(
					"title" => "Module:  Chart (Gauge)",
					"type" => "chart",
					"chart" => "gauge",
					"height" => 180,
					"colors" => array("#FF0000", "#F97600", "#F6C600", "#60B044"),
					"thresholds" => array(30, 60, 90, 100),
					"data" => array("value" => array(91.4)),
					"desc" => "Description for gauge.  This feature requires Admin Pack Modules to be installed."
				),
			),
			"submit" => "Save",
			"focus" => true
		);

		BB_GeneratePage("Some Page", $menuopts, $contentopts);
	}
	else if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "somepage2_getinfo")
	{
		$id = (int)$_REQUEST["id"];

?>
<script type="text/javascript">
setTimeout(function() {
	$('#mainitem_<?=$id?>').remove();
	BB_StripeSidebar();
}, 2000);

$('#maincontentwrap').html('Loaded item #<b><?=$id?></b> and the item will be removed from the sidebar in two seconds.');
</script>
<?php
	}
	else if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "somepage2")
	{
		$items = array();
		for ($x = 0; $x < 1000; $x++)
		{
			$items[] = array(
				"id" => "mainitem_" . $x,
				"display" => "Item #" . $x,
				"onclick" => "LoadItem('" . $x . "');"
			);
		}

		ob_start();
?>
<script type="text/javascript">
function LoadItem(id) {
	$('#ajaxhidden').load('index.php', {
		'action' : 'somepage2_getinfo',
		'id' : id,
		'sec_t' : '<?=BB_CreateSecurityToken("somepage2_getinfo")?>'
	});
}
</script>
<?php
		$js = ob_get_contents();
		ob_end_clean();

		$contentopts = array(
			"items" => $items,
			"topbarhtml" => "An optional <b>top bar</b>",
			"initialhtml" => "<i>Select an option from the left.</i>",
			"bottombarhtml" => "An optional <b>bottom bar</b>",
			"javascript" => $js
		);

		BB_GenerateBulkEditPage("Some Bulk Edit Page", $contentopts);
	}
	else
	{
		$contentopts = array(
			"desc" => "Pick an option from the menu."
		);

		BB_GeneratePage("Home", $menuopts, $contentopts);
	}

	// [Put any cleanup/finalization logic here.]
?>