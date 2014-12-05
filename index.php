<?php
	// Admin Pack.
	// (C) 2014 CubicleSoft.  All Rights Reserved.

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
		"Temp Title/Section" => array(
			"Some Page" => BB_GetRequestURLBase() . "?action=somepage&sec_t=" . BB_CreateSecurityToken("somepage")
		)
	);

	if (isset($_REQUEST["action"]) && $_REQUEST["action"] == "somepage")
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

		$contentopts = array(
			"desc" => "This is some page.",
			"nonce" => "action",
			"hidden" => array(
				"action" => "somepage"
			),
			"fields" => array(
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
					"default" => $somevar,
					"desc" => "Description for Field 2."
				),
				array(
					"title" => "Date",
					"type" => "date",
					"name" => "date",
					"default" => date("Y-m-d"),
					"desc" => "Description for Date."
				),
				array(
					"title" => "File",
					"type" => "file",
					"name" => "file",
					"desc" => "Description for File."
				),
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
				array(
					"title" => "Field 7",
					"type" => "select",
					"multiple" => true,
					"mode" => "dropdown",
					"height" => "250px",
					"name" => "field7",
					"options" => array("name" => "Name", "email" => "E-mail Address", "phone" => "Phone Number"),
					"default" => array(),
					"desc" => "Description for Field 7."
				),
			),
			"submit" => "Save",
			"focus" => true
		);

		BB_GeneratePage("Some Page", $menuopts, $contentopts);
	}
	else
	{
		$contentopts = array(
			"desc" => "Pick an option from the left."
		);

		BB_GeneratePage("Home", $menuopts, $contentopts);
	}

	// [Put any cleanup/finalization logic here.]
?>