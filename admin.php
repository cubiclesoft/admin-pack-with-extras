<?php
	// Admin Pack.
	// (C) 2017 CubicleSoft.  All Rights Reserved.

	// This small package exists to make it easy to design quick-n-dirty administrative backends that look good.
	// This file is well-commented.  When republishing based on this work, copyrights must remain intact.
	// Otherwise, feel free to delete the comments and make the necessary changes to make it work.
	// The obvious shouldn't have to be stated, but you need to know PHP to use this package.

	require_once "support/str_basics.php";
	require_once "support/page_basics.php";

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
		"Options" => array(
			// Replace these menu options with your own.  The examples contain useful boilerplate code.
			"Manage" => BB_GetRequestURLBase() . "?action=manageexample&sec_t=" . BB_CreateSecurityToken("manageexample"),
			"Add Entry" => BB_GetRequestURLBase() . "?action=addeditexample&sec_t=" . BB_CreateSecurityToken("addeditexample"),
			"Bulk Edit" => BB_GetRequestURLBase() . "?action=bulkeditexample&sec_t=" . BB_CreateSecurityToken("bulkeditexample"),
			"View/Print" => BB_GetRequestURLBase() . "?action=viewprintexample&id=1&sec_t=" . BB_CreateSecurityToken("viewprintexample"),
			"No Menu" => BB_GetRequestURLBase() . "?action=nomenuexample&sec_t=" . BB_CreateSecurityToken("nomenuexample")
		)
	);

	// Optional function to customize styles.
	function BB_InjectLayoutHead()
	{
		// Menu title underline:  Colors with 60% saturation and 75% brightness generally look good.
?>
<style type="text/css">
#menuwrap .menu .title { border-bottom: 2px solid #C48851; }
</style>
<?php
	}

	// An example function used later on to demonstrate loading user information from a database.
	function LoadUserDetails($info)
	{
		$defaults = array(
			"first" => "John", "last" => "Smith", "email" => "", "city" => "", "state" => "", "zip" => "",
			"status" => "Approved", "notes" => "What a great guy.\n\nUser approved on " . date("m/d/Y") . ".", "othernotes" => ""
		);

		return BB_ProcessInfoDefaults($info, $defaults);
	}

	if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "deleteexample")
	{
		$id = (isset($_REQUEST["id"]) ? (int)$_REQUEST["id"] : 0);
//		$db->Query("DELETE FROM userdetails WHERE id = ?", array($id));

		BB_RedirectPage("success", "Successfully deleted the details entry.  (Just imagine that it worked.  After all, this is only an example.)", array("action=manageexample&sec_t=" . BB_CreateSecurityToken("manageexample")));
	}
	else if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "manageexample")
	{
		// Demonstrates a common pattern to show all entries in a MySQL/MariaDB database and provide options.

//		$rows = array();
//		$result = $db->Query("SELECT * FROM userdetails");
//		while ($row = $result->NextRow())
//		{
//			$info = LoadUserDetails(unserialize($row->info));
//
//			$rows[] = array(htmlspecialchars($info["first"]), htmlspecialchars($info["last"]), "<a href=\"" . BB_GetRequestURLBase() . "?action=viewprintexample&id=" . $row->id . "&sec_t=" . BB_CreateSecurityToken("viewprintexample") . "\">View</a> | <a href=\"" . BB_GetRequestURLBase() . "?action=addeditexample&id=" . $row->id . "&sec_t=" . BB_CreateSecurityToken("addeditexample") . "\">Edit</a> | <a href=\"" . BB_GetRequestURLBase() . "?action=deleteexample&id=" . $row->id . "&sec_t=" . BB_CreateSecurityToken("deleteexample") . "\" onclick=\"return confirm('Are you sure you want to delete these details?');\">Delete</a>");
//		}

		// A fake entry to show what this pattern looks like.
		$info = LoadUserDetails(array());
		$rows[] = array(htmlspecialchars($info["first"]), htmlspecialchars($info["last"]), "<a href=\"" . BB_GetRequestURLBase() . "?action=viewprintexample&id=1&sec_t=" . BB_CreateSecurityToken("viewprintexample") . "\">View</a> | <a href=\"" . BB_GetRequestURLBase() . "?action=addeditexample&id=1&sec_t=" . BB_CreateSecurityToken("addeditexample") . "\">Edit</a> | <a href=\"" . BB_GetRequestURLBase() . "?action=deleteexample&id=1&sec_t=" . BB_CreateSecurityToken("deleteexample") . "\" onclick=\"return confirm('Are you sure you want to delete these details?');\">Delete</a>");

		// Custom HTML for a mini-menu.
		$desc = "<br>";
		$desc .= "<a href=\"" . BB_GetRequestURLBase() . "?action=addeditexample&sec_t=" . BB_CreateSecurityToken("addeditexample") . "\">Add New Entry</a>";

		$contentopts = array(
			"desc" => "Manage user details.",
			"htmldesc" => $desc,
			"fields" => array(
				array(
					"type" => "table",
					"cols" => array("First", "Last", "Options"),
					"rows" => $rows
				)
			)
		);

		BB_GeneratePage("Manage Entries Example", $menuopts, $contentopts);
	}
	else if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "addeditexample_simple")
	{
		// A simple boilerplate version of 'addeditexample' designed for copy/pasting.

		$id = (isset($_REQUEST["id"]) ? (int)$_REQUEST["id"] : 0);
		$row = $db->GetRow("SELECT * FROM userdetails WHERE id = ?", array($id));
		if ($row)  $info = LoadUserDetails(unserialize($row->info));
		else
		{
			$info = LoadUserDetails(array());
			$id = 0;
		}

		if (isset($_REQUEST["first"]))
		{
			if ($_REQUEST["first"] == "")  BB_SetPageMessage("error", "Please fill in 'Field 1'.", "first");

			if (BB_GetPageMessageType() != "error")
			{
				// [Save data here.]
				$originfo = $info;
				$info["first"] = $_REQUEST["first"];

				if ($id)  $db->Query("UPDATE userdetails SET info = ? WHERE id = ?", array(serialize($info), $id));
				else  $db->Query("INSERT INTO userdetails SET info = ?", array(serialize($info)));

				BB_RedirectPage("success", ($_REQUEST["id"] > 0 ? "Successfully saved the details." : "Successfully created the entry."), array("action=manageexample&sec_t=" . BB_CreateSecurityToken("manageexample")));
			}
		}

		$contentopts = array(
			"desc" => ($id ? "Edit the user details." : "Add user details."),
			"hidden" => array(
				"id" => $id
			),
			"fields" => array(
				array(
					"title" => "Field 1",
					"type" => "text",
					"name" => "first",
					"default" => $info["first"],
					"desc" => "Basic text field."
				),
			),
			"submit" => ($id ? "Save" : "Create")
		);

		BB_GeneratePage(($id ? "Edit Entry Example" : "Add Entry Example"), $menuopts, $contentopts);
	}
	else if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "addeditexample")
	{
		// Demonstrates a common pattern to easily add new entries AND edit existing entries in a MySQL/MariaDB database with writing code only one time.
		// Less code results in fewer logic errors.  Some code is commented out so that the example actually functions.
		// Includes logic to test the most common FlexForms features, so this might be a bit excessive.  Use the 'addeditexample_simple' code above for rapid start development.

		$id = (isset($_REQUEST["id"]) ? (int)$_REQUEST["id"] : 0);
//		$row = $db->GetRow("SELECT * FROM userdetails WHERE id = ?", array($id));
//		if ($row)  $info = LoadUserDetails(unserialize($row->info));
//		else
//		{
			$info = LoadUserDetails(array());
//			$id = 0;
//		}

		// Example handler for the file uploader FlexForms Module.
		// Note:  It is far easier to only support file uploads when editing existing items.
		//        Otherwise, temporary file management will need to be implemented via user sessions with server-side cron cleanup if the file is abandoned.
		if (isset($_REQUEST["fileuploader"]))
		{
			header("Content-Type: application/json");

			$allowedexts = array(
				"jpg" => true,
				"png" => true,
				"gif" => true,
			);

			$files = BB_NormalizeFiles("file2");
			if (!isset($files[0]))  $result = array("success" => false, "error" => "File data was submitted but is missing.", "errorcode" => "bad_input");
			else if (!$files[0]["success"])  $result = $files[0];
			else if (!isset($allowedexts[strtolower($files[0]["ext"])]))
			{
				$result = array(
					"success" => false,
					"error" => "Invalid file extension.  Must be '.jpg', '.png', or '.gif'.",
					"errorcode" => "invalid_file_ext"
				);
			}
			else
			{
				if (file_exists("support/flex_forms_fileuploader.php"))  require_once "support/flex_forms_fileuploader.php";

				// For chunked file uploads, get the current filename and starting position from the incoming headers.
				$name = FlexForms_FileUploader::GetChunkFilename();
				if ($name !== false)
				{
					$startpos = FlexForms_FileUploader::GetFileStartPosition();

					// [Do stuff with the file chunk.]
				}
				else
				{
					// [Do stuff with the file here.]
					// copy($files[0]["file"], __DIR__ . "/images/" . $id . "." . strtolower($files[0]["ext"]));
				}

				$result = array(
					"success" => true
				);
			}

			echo json_encode($result, JSON_UNESCAPED_SLASHES);
			exit();
		}

		if (isset($_REQUEST["first"]))
		{
			if ($_REQUEST["first"] == "")  BB_SetPageMessage("error", "Please fill in 'Field 1'.", "first");
			if ($_REQUEST["last"] == "")  BB_SetPageMessage("error", "Please fill in 'Field 2'.", "last");

			if (BB_GetPageMessageType() != "error")
			{
				// [Save data here.]
				$originfo = $info;
				$info["first"] = $_REQUEST["first"];
				$info["last"] = $_REQUEST["last"];
//				$info["email"] = $_REQUEST["email"];

//				if ($id)  $db->Query("UPDATE userdetails SET email = ?, info = ? WHERE id = ?", array($info["email"], serialize($info), $id));
//				else  $db->Query("INSERT INTO userdetails SET email = ?, info = ?", array($info["email"], serialize($info)));

				BB_RedirectPage("success", ($_REQUEST["id"] > 0 ? "Successfully saved the details." : "Successfully created the entry."), array("action=manageexample&sec_t=" . BB_CreateSecurityToken("manageexample")));
			}
		}

		// [Do processing here to generate any dynamic content options.]
		$somevar = "default value 2";
		$items = array("Furries", "Fuzzies", "Fluffies", "Puppies", "Kitties", "Penguins", "Ponies", "Tribbles", "Unicorns");
		$itemdescs = array("So much pretty.", "Everyone loves fuzzy things.", "Worthy of face burying.", "There's a lot to say about teh puppies and so there might be some wrapping of this line.", "Soakin' up teh suns.  Acceptin' teh belly rubs.", "Adorbs.", "Will you be my neigh-bor?", "Delicious.", "I'm a barbequein' with mah unicorn!");
		$rows = array();
		foreach ($items as $num => $item)
		{
			$rows[] = array(($num + 1), htmlspecialchars($item), htmlspecialchars($itemdescs[$num]), date("F, j Y @ g:i a"), "<a href=\"" . BB_GetRequestURLBase() . "?action=addeditanimals&id=" . ($num + 1) . "&sec_t=" . BB_CreateSecurityToken("addeditanimals") . "\">Edit</a>");
		}

		// Load and use most FlexForms Modules if available.
		if (file_exists("support/flex_forms_calendar_table.php"))  require_once "support/flex_forms_calendar_table.php";
		if (file_exists("support/flex_forms_chart.php"))  require_once "support/flex_forms_chart.php";
		if (file_exists("support/flex_forms_tablefilter.php"))  require_once "support/flex_forms_tablefilter.php";
		if (file_exists("support/flex_forms_htmledit.php"))  require_once "support/flex_forms_htmledit.php";
		if (file_exists("support/flex_forms_textcounter.php"))  require_once "support/flex_forms_textcounter.php";
		if (file_exists("support/flex_forms_fileuploader.php"))  require_once "support/flex_forms_fileuploader.php";

		$tomorrow = mktime(0, 0, 0, date("n"), date("j") + 1);

		$contentopts = array(
			"desc" => ($id ? "Edit the user details." : "Add user details."),
			"hidden" => array(
				"id" => $id
			),
			"fields" => array(
				// The accordions only show correctly when FlexForms Extras is used.
				array(
					"title" => "Some Options",
					"type" => "accordion"
				),
				// Field 1 demonstrates current "best practice".
				array(
					"title" => "Field 1",
					"type" => "text",
					"name" => "first",
					"default" => $info["first"],
					"desc" => "Basic text field."
				),
				array(
					"title" => "Field 2",
					"type" => "text",
					"name" => "last",
					"value" => BB_GetValue("last", $info["last"]),
					// ^^^ Using 'value' directly is an older solution.  Using 'default' usually accomplishes the same thing with less code (see 'Field 1' above).
					"desc" => "Another text field."
				),
				array(
					"title" => "Some More Options",
					"type" => "accordion"
				),
				"startrow",
				array(
					"title" => "Field 2a",
					"type" => "text",
					"width" => "500px",
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
				array(
					"title" => "Field 2c",
					"type" => "text",
					"width" => "150px",
					"name" => "field2c",
					"default" => "",
				),
				"endrow",
				array(
					"title" => "Date",
					"type" => "date",
					"name" => "date",
					"default" => date("Y-m-d"),
					"desc" => "Description for Date.  Requires FlexForms Extras to show this field."
				),
				array(
					"title" => "Table",
					"split" => false,
					"type" => "table",
					"cols" => array("ID", "Type", "Description", "Date", "Options"),
					"nowrap" => array("Date"),
					"rows" => $rows,
					"card" => array(
						"width" => 800,
						"extracols" => array(1),
						"head" => "Info/Options",
						"body" => "<b>#%2 - %3</b><br>%5<br>%6"
					),
					"order" => "Order",
					"bodyscroll" => true,
					"desc" => "Description for Table.  When used with FlexForms Extras, responsive table cards, drag-and-drop, and responsive body scroll support are enabled."
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
					"title" => "Select",
					"type" => "select",
					"name" => "field2e",
					"options" => array("name" => "Name", "email" => "E-mail Address", "phone" => "Phone Number"),
					"default" => array("name"),
					"desc" => "Description for Select."
				),
				array(
					"title" => "Cookies?",
					"type" => "checkbox",
					"name" => "field2e",
					"value" => "Yes",
					"display" => "I like cookies",
					"default" => true
				),
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
				array(
					"title" => "Informational",
					"type" => "static",
					"value" => "Did you know that FlexForms, which powers CubicleSoft Admin Pack, actually has its origins in Barebones CMS?  BB_PropertyForm() started it all."
				),
				array(
					"title" => "Admin Notes",
					"type" => "textarea",
					"name" => "notes",
					"default" => $info["notes"]
				),
				array(
					"title" => "Custom HTML Output",
					"type" => "custom",
					"value" => "<div class=\"staticwrap\"><a href=\"https://www.youtube.com/watch?v=dQw4w9WgXcQ&ts=" . time() . "\" target=\"_blank\">Give HTML some lovin'</a></div>"
				),
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
//					// ^^^ Using 'select' directly is an older solution.  Using 'default' accomplishes the same thing with less code (see 'Field 7' and 'Field 8' below).
//					"desc" => "Description for Field 7.  FlexForms Extras turns this field into a multiselect 'dropdown' widget."
//				),
				array(
					"title" => "Field 7",
					"type" => "select",
					"multiple" => true,
					"mode" => "flat",
					"height" => "250px",
					"name" => "field7",
					"options" => array("name" => "Name", "email" => "E-mail Address", "phone" => "Phone Number"),
					"default" => array("name"),
					"desc" => "Description for Field 7.  FlexForms Extras turns this field into a multiselect 'flat' widget."
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
					"desc" => "Description for Field 8.  FlexForms Extras turns this field into a multiselect 'tags' widget."
				),
				"split",
				array(
					"title" => "Module:  Calendar Table",
					"type" => "calendar",
					"startyear" => date("Y"),
					"startmonth" => date("m"),
					"endyear" => date("Y"),
					"endmonth" => date("m"),
					"cols" => array("Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"),
					"data" => array(date("Y-m-d", $tomorrow) => "<a href=\"https://google.com/?q=" . urlencode("things to do on " . date("l, F j, Y", $tomorrow)) . "\" target=\"_blank\"><b>" . date("j", $tomorrow) . "</b></a>"),
					"desc" => "Description for calendar.  This feature requires FlexForms Modules to be included."
				),
				array(
					"title" => "Module:  Chart (Line)",
					"type" => "chart",
					"chart" => "line",
					"data" => array("key1" => array(1, 5, 10, 7, 6, 9, 8), "key2" => array(10, 6, 3, 4, 5, 6, 7)),
					"options" => array("grid.x.show" => true, "zoom.enabled" => true),
					"desc" => "Description for line chart.  This feature requires FlexForms Modules to be included."
				),
				array(
					"title" => "Module:  Chart (Pie)",
					"type" => "chart",
					"chart" => "pie",
					"data" => array("key1" => array(1, 5, 10, 7, 6, 9, 8), "key2" => array(10, 6, 3, 4, 5, 6, 7)),
					"desc" => "Description for pie chart.  This feature requires FlexForms Modules to be included."
				),
				array(
					"title" => "Module:  Chart (Gauge)",
					"type" => "chart",
					"chart" => "gauge",
					"height" => 180,
					"colors" => array("#FF0000", "#F97600", "#F6C600", "#60B044"),
					"thresholds" => array(30, 60, 90, 100),
					"data" => array("value" => array(91.4)),
					"desc" => "Description for gauge.  This feature requires FlexForms Modules to be included."
				),
				array(
					"title" => "Module:  Table Filter",
					"type" => "table",
					"cols" => array("ID", "Type", "Description", "Date", "Options"),
					"rows" => $rows,
//					"cardwidth" => 620,
					"card" => "<b>#%1 - %2</b><br>%4<br>%3<br>%5",
					"bodyscroll" => true,
					"filter" => true,
					"desc" => "Description for table.  Try typing 'ies' into the filter field.  This feature requires FlexForms Modules to be included."
				),
				array(
					"title" => "Module:  HTML Editor",
					"type" => "textarea",
					"width" => "100%",
					"height" => "300px",
					"name" => "htmledit",
					"default" => "<p><b>Why, hello there!</b></p><p>Have you had your Wheaties today?</p>",
					"html" => true,
					"desc" => "Description for HTML editor.  This feature requires FlexForms Modules to be included."
				),
				array(
					"title" => "Module:  Text Counter",
					"type" => "text",
					"name" => "textcount",
					"default" => "",
					"counter" => 150,
					"desc" => "Description for Text Counter.  This feature requires FlexForms Modules to be included."
				),
				array(
					"title" => "Module:  File Uploader",
					"type" => "file",
					"name" => "file2",
					"accept" => ".jpg, .png, .gif, image/jpeg, image/png, image/gif",
					"multiple" => true,
					"uploader" => true,
//					"maxchunk" => min(FlexForms_FileUploader::GetMaxUploadFileSize(), 1000000),
					"desc" => "Description for File Uploader.  This feature requires FlexForms Modules to be included."
				),
			),
			"submit" => ($id ? "Save" : "Create")
		);

		// Rarely used modules.  Also demonstrates adding fields dynamically.
		if (file_exists("support/flex_forms_passwordmanager.php"))
		{
			require_once "support/flex_forms_passwordmanager.php";

			$contentopts["fields"][] = array(
				"title" => "Module:  Stop Password Manager",
				"type" => "password",
				"name" => "signature",
				"default" => "",
				"passwordmanager" => false,
				"desc" => "Description for Stop Password Manager.  This feature requires FlexForms Modules to be included."
			);
		}

		BB_GeneratePage(($id ? "Edit Entry Example" : "Add Entry Example"), $menuopts, $contentopts);
	}
	else if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "bulkeditexample_getinfo")
	{
		$id = (int)$_REQUEST["id"];

?>
<script type="text/javascript">
setTimeout(function() {
	$('#mainitem_<?=$id?>').remove();
	BB_StripeSidebar();
}, 2000);

$('#maincontentwrap').html('Loaded item #<b><?=$id?></b> and the item will be removed from the sidebar in two seconds.  Items in the sidebar can be inserted/updated/removed dynamically.');
</script>
<?php
	}
	else if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "bulkeditexample")
	{
		// Demonstrates a common pattern for bulk editing highly visual content (e.g. placing pins on a map).

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
	$('#ajaxhidden').load('<?=BB_GetRequestURLBase()?>', {
		'action' : 'bulkeditexample_getinfo',
		'id' : id,
		'sec_t' : '<?=BB_CreateSecurityToken("bulkeditexample_getinfo")?>'
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

		BB_GenerateBulkEditPage("Example Bulk Edit Page", $contentopts);
	}
	else if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "viewprintexample")
	{
		// Demonstrates a common pattern to display a summary view that is also print-ready.
		// The view/print layout adds additional features to FlexForms.

		$id = (isset($_REQUEST["id"]) ? (int)$_REQUEST["id"] : 0);
//		$row = $db->GetRow("SELECT * FROM userdetails WHERE id = ?", array($id));
//		if ($row)
//		{
//			$info = LoadUserDetails(unserialize($row->info));

			// Using fake user details here so that the example works.
			$info = LoadUserDetails(array());

			require_once "support/view_print_layout.php";

			// Any extra/custom HTML goes here.
			$desc = "";

			$contentopts = array(
				"desc" => "",
				"htmldesc" => $desc,
				"fields" => array(
					array(
						"type" => "viewmedia",
						"value" => "<img class=\"mediaitem\" style=\"border: 1px dotted #CCCCCC;\" src=\"photos/" . $id . ".jpg\">",
						"desc" => "A floating image.  Well, it would show if the file existed."
					),
					array(
						"type" => "viewtable",
						"data" => array(
							"First" => $info["first"],
							"Last" => $info["last"],
							"E-mail" => $info["email"],
							"Status" => $info["status"]
						),
						"desc" => "Shows first name, last name, and status but not e-mail since 'compact' data mode is enabled (the default).  Values are also HTML encoded by default for safety."
					),
					"endmedia",
					array(
						"use" => ($info["notes"] != ""),
						"title" => "Admin Notes",
						"type" => "viewstatic",
						"value" => $info["notes"]
					),
					array(
						"use" => ($info["othernotes"] != ""),
						"title" => "Other Notes",
						"type" => "viewstatic",
						"value" => $info["othernotes"],
						"desc" => "This field won't show at all since 'use' is false.  Value for viewstatic is HTML encoded by default."
					)
				)
			);

			BB_GeneratePage("View Entry Example for #" . $id, array(), $contentopts);
//		}
	}
	else if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "nomenuexample")
	{
		// Demonstrates a common pattern for displaying the standard layout without menu options to the user.

		if (isset($_REQUEST["username"]))
		{
			BB_SetPageMessage("error", "Invalid username or password.");
		}

		$contentopts = array(
			"desc" => "Please enter some login information.",
			"htmldesc" => "<br><br><b>IMPORTANT:  Please do not submit real usernames or passwords as this is just an example.</b>",
			"fields" => array(
				array(
					"title" => "Username",
					"type" => "text",
					"name" => "username",
					"default" => ""
				),
				array(
					"title" => "Password",
					"type" => "password",
					"name" => "password",
					"default" => ""
				),
			),
			"submit" => "Login"
		);

		BB_GeneratePage("Login", array(), $contentopts);
	}
	else
	{
		$contentopts = array(
			"desc" => "Pick an option from the menu."
		);

		BB_GeneratePage("Home", $menuopts, $contentopts);
	}
?>