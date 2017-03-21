<?php
	// Admin Pack server-side extras.
	// (C) 2017 CubicleSoft.  All Rights Reserved.

	class AdminPackExtras
	{
		// Date fields.
		public static function DateInit(&$state, &$options)
		{
			$state["extras_date"] = false;
		}

		public static function DateFieldType(&$state, $num, &$field)
		{
			if ($field["type"] === "date")
			{
				$id = "f" . $num . "_" . $field["name"];

				if ($state["autofocus"] === false)  $state["autofocus"] = htmlspecialchars($id);
?>
			<input class="date"<?php if (isset($field["width"]))  echo " style=\"width: " . htmlspecialchars($field["width"]) . "\""; ?> type="text" id="<?php echo htmlspecialchars($id); ?>" name="<?php echo htmlspecialchars($field["name"]); ?>" value="<?php echo htmlspecialchars($field["value"]); ?>" />
<?php

				// Queue up the necessary Javascript for later output.
				ob_start();
				if ($state["extras_date"] === false)
				{
					$state["jqueryuiused"] = true;

					$state["extras_date"] = "";
				}

				$options = array("dateFormat" => "yy-mm-dd");

				// Allow the datepicker to be fully customized beyond basic support.
				// Valid options:  http://api.jqueryui.com/datepicker/
				if (isset($field["options"]))
				{
					foreach ($field["options"] as $key => $val)  $options[$key] = $val;
				}

?>
	<script type="text/javascript">
	$(function() {
		var options = <?php echo json_encode($options, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); ?>;
<?php
				if (isset($field["callbacks"]))
				{
					foreach ($field["callbacks"] as $key => $val)
					{
?>
		options['<?php echo $key; ?>'] = <?php echo $val; ?>;
<?php
					}
				}
?>

		if (jQuery.fn.datepicker)  $('#<?php echo BB_JSSafe($id); ?>').datepicker(options);
		else  alert('<?php echo BB_JSSafe(BB_Translate("Warning:  Missing jQuery UI for date field.\n\nThis feature requires AdminPack Extras.")); ?>');
	});
	</script>
<?php
				$state["extras_date"] .= ob_get_contents();
				ob_end_clean();
			}
		}

		public static function DateFinalize(&$state)
		{
			if ($state["extras_date"] !== false)  echo $state["extras_date"];
		}


		// Accordions.
		public static function AccordionsInit(&$state, &$options)
		{
			$state["extras_accordion"] = false;
			$state["extras_insideaccordion"] = false;

			$state["customfieldtypes"]["accordion"] = true;
			$state["customfieldtypes"]["accordian"] = true;
		}

		public static function AccordionsFieldString(&$state, $num, &$field)
		{
			if (($field == "endaccordion" || $field == "endaccordian") && $state["extras_insideaccordion"])
			{
				if ($state["insiderow"])
				{
?>
			</tr></table></div>
<?php
					$state["insiderow"] = false;
				}
?>
				</div>
			</div>
<?php
				$state["extras_insideaccordion"] = false;
			}
			else if ($field == "nosplit")
			{
				if ($state["extras_insideaccordion"])  $state["firstitem"] = true;
			}
		}

		public static function AccordionsCustomFieldType(&$state, $num, &$field)
		{
			if ($field["type"] == "accordion" || $field["type"] == "accordian")
			{
				if ($state["insiderow"])
				{
?>
			</tr></table></div>
<?php
					$state["insiderow"] = false;
				}

				if ($state["extras_insideaccordion"])
				{
?>
				</div>
				<h3><?php echo htmlspecialchars(BB_Translate($field["title"])); ?></h3>
				<div class="formaccordionitems">
<?php
				}
				else
				{
					$id = "accordion" . $num;
?>
			<div class="formaccordionwrap" id="<?php echo htmlspecialchars($id); ?>">
				<h3><?php echo htmlspecialchars(BB_Translate($field["title"])); ?></h3>
				<div class="formaccordionitems">
<?php
					$state["extras_insideaccordion"] = true;

					// Queue up the necessary Javascript for later output.
					ob_start();
					if ($state["extras_accordion"] === false)
					{
						$state["jqueryuiused"] = true;

						$state["extras_accordion"] = "";
					}

					$options = array(
						"collapsible" => true,
						"active" => false,
						"heightStyle" => "content"
					);

					// Allow each accordion to be fully customized beyond basic support.
					// Valid options:  http://api.jqueryui.com/accordion/
					if (isset($field["options"]))
					{
						foreach ($field["options"] as $key => $val)  $options[$key] = $val;
					}

?>
	<script type="text/javascript">
	$(function() {
		var options = <?php echo json_encode($options, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); ?>;
<?php
				if (isset($field["callbacks"]))
				{
					foreach ($field["callbacks"] as $key => $val)
					{
?>
		options['<?php echo $key; ?>'] = <?php echo $val; ?>;
<?php
					}
				}
?>

		if (jQuery.fn.accordion)  $('#<?php echo BB_JSSafe($id); ?>').accordion(options);
		else  alert('<?php echo BB_JSSafe(BB_Translate("Warning:  Missing jQuery UI for accordion.\n\nThis feature requires AdminPack Extras.")); ?>');
	});
	</script>
<?php

					$state["extras_accordion"] .= ob_get_contents();
					ob_end_clean();
				}

				$state["firstitem"] = true;
			}
		}

		public static function AccordionsFinalize(&$state)
		{
			if ($state["extras_accordion"] !== false)
			{
				if ($state["extras_insideaccordion"])
				{
?>
				</div>
			</div>
<?php
				}

				echo $state["extras_accordion"];
			}
		}


		// Multiselect.
		public static function MultiselectInit(&$state, &$options)
		{
			$state["extras_multiselect_tags"] = false;
			$state["extras_multiselect_dropdown"] = false;
			$state["extras_multiselect_dropdown_height"] = 200;
			$state["extras_multiselect_flat"] = false;
		}

		public static function MultiselectFieldType(&$state, $num, &$field)
		{
			global $bb_formtables;

			if ($field["type"] == "select" && (isset($field["multiple"]) && $field["multiple"] === true) && isset($field["mode"]))
			{
				$idbase = htmlspecialchars("f" . $num . "_" . $field["name"]);

				if ($field["mode"] == "tags")
				{
					$state["jqueryuiused"] = true;

					// Queue up the necessary Javascript for later output.
					ob_start();
					if ($state["extras_multiselect_tags"] === false)
					{
?>
	<link rel="stylesheet" href="<?php echo htmlspecialchars($state["rooturl"] . "/" . $state["supportpath"] . "/multiselect-select2/select2.css"); ?>" type="text/css" media="all" />
	<script type="text/javascript" src="<?php echo htmlspecialchars($state["rooturl"] . "/" . $state["supportpath"] . "/multiselect-select2/select2.min.js"); ?>"></script>
<?php

						$state["extras_multiselect_tags"] = "";
					}

					$options = array("__adminpack" => true);
					if (isset($field["mininput"]))  $options["minimumInputLength"] = (int)$field["mininput"];

					// Allow each select2 instance to be fully customized beyond basic support.
					// Valid options:  https://select2.github.io/options.html
					if (isset($field["multiselect_options"]))
					{
						foreach ($field["multiselect_options"] as $key => $val)  $options[$key] = $val;
					}

?>
	<script type="text/javascript">
	$(function() {
		var options = <?php echo json_encode($options, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); ?>;
<?php
					if (isset($field["multiselect_callbacks"]))
					{
						foreach ($field["multiselect_callbacks"] as $key => $val)
						{
?>
		options['<?php echo $key; ?>'] = <?php echo $val; ?>;
<?php
						}
					}
?>

		if (jQuery.fn.select2)  $('#<?php echo BB_JSSafe($idbase); ?>').select2(options);
		else  alert('<?php echo BB_JSSafe(BB_Translate("Warning:  Missing jQuery UI select2 for multiple selection field.\n\nThis feature requires AdminPack Extras.")); ?>');
	});
	</script>
<?php
					$state["extras_multiselect_tags"] .= ob_get_contents();
					ob_end_clean();

					$field["mode"] = "formhandler";
				}
				else if ($field["mode"] == "dropdown" && $state["extras_multiselect_flat"] === false)
				{
					if (isset($field["height"]))  $state["extras_multiselect_dropdown_height"] = (int)$field["height"];

					$state["jqueryuiused"] = true;

					// Queue up the necessary Javascript for later output.
					ob_start();
					if ($state["extras_multiselect_dropdown"] === false)
					{
?>
	<link rel="stylesheet" href="<?php echo htmlspecialchars($state["rooturl"] . "/" . $state["supportpath"] . "/multiselect-widget/jquery.multiselect.css"); ?>" type="text/css" media="all" />
	<link rel="stylesheet" href="<?php echo htmlspecialchars($state["rooturl"] . "/" . $state["supportpath"] . "/multiselect-widget/jquery.multiselect.filter.css"); ?>" type="text/css" media="all" />
	<script type="text/javascript" src="<?php echo htmlspecialchars($state["rooturl"] . "/" . $state["supportpath"] . "/multiselect-widget/jquery.multiselect.min.js"); ?>"></script>
	<script type="text/javascript" src="<?php echo htmlspecialchars($state["rooturl"] . "/" . $state["supportpath"] . "/multiselect-widget/jquery.multiselect.filter.js"); ?>"></script>
<?php

						$state["extras_multiselect_dropdown"] = "";
					}

					$options = array(
						"selectedText" => BB_Translate("# of # selected"),
						"selectedList" => 5,
						"height" => $state["extras_multiselect_dropdown_height"],
						"position" => array(
							"my" => "left top",
							"at" => "left bottom",
							"collision" => "flip"
						)
					);

					// Allow each multiselect instance to be fully customized beyond basic support.
					// Valid options:  http://www.erichynds.com/blog/jquery-ui-multiselect-widget
					if (isset($field["multiselect_options"]))
					{
						foreach ($field["multiselect_options"] as $key => $val)  $options[$key] = $val;
					}

?>
	<script type="text/javascript">
	$(function() {
		var options = <?php echo json_encode($options, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); ?>;
<?php
					if (isset($field["multiselect_callbacks"]))
					{
						foreach ($field["multiselect_callbacks"] as $key => $val)
						{
?>
		options['<?php echo $key; ?>'] = <?php echo $val; ?>;
<?php
						}
					}
?>

		if (jQuery.fn.multiselect && jQuery.fn.multiselectfilter)  $('#<?php echo BB_JSSafe($idbase); ?>').multiselect(options).multiselectfilter();
		else  alert('<?php echo BB_JSSafe(BB_Translate("Warning:  Missing jQuery UI multiselect widget or multiselectfilter for dropdown multiple selection field.\n\nThis feature requires AdminPack Extras.")); ?>');
	});
	</script>
<?php
					$state["extras_multiselect_dropdown"] .= ob_get_contents();
					ob_end_clean();

					$field["mode"] = "formhandler";
				}
				else if ($field["mode"] == "flat" && $state["extras_multiselect_dropdown"] === false)
				{
					$state["jqueryuiused"] = true;

					// Queue up the necessary Javascript for later output.
					ob_start();
					if ($state["extras_multiselect_flat"] === false)
					{
?>
	<link rel="stylesheet" href="<?php echo htmlspecialchars($state["rooturl"] . "/" . $state["supportpath"] . "/multiselect-flat/css/jquery.uix.multiselect.css"); ?>" type="text/css" media="all" />
	<script type="text/javascript" src="<?php echo htmlspecialchars($state["rooturl"] . "/" . $state["supportpath"] . "/multiselect-flat/js/jquery.uix.multiselect.js"); ?>"></script>
<?php

						$state["extras_multiselect_flat"] = "";
					}

					$options = array(
						"availableListPosition" => ($bb_formtables ? "left" : "top"),
						"sortable" => true,
						"sortMethod" => NULL
					);

					// Allow each multiselect instance to be fully customized beyond basic support.
					// Valid options:  https://github.com/yanickrochon/jquery.uix.multiselect/wiki/API-Documentation
					if (isset($field["multiselect_options"]))
					{
						foreach ($field["multiselect_options"] as $key => $val)  $options[$key] = $val;
					}

?>
	<script type="text/javascript">
	$(function() {
		if (jQuery.fn.multiselect)
		{
			var options = <?php echo json_encode($options, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); ?>;
<?php
				if (isset($field["multiselect_callbacks"]))
				{
					foreach ($field["multiselect_callbacks"] as $key => $val)
					{
?>
		options['<?php echo $key; ?>'] = <?php echo $val; ?>;
<?php
					}
				}
?>

			$('#<?php echo BB_JSSafe($idbase); ?>').multiselect(options);
			$(window).resize(function() {
				$('#<?php echo BB_JSSafe($idbase); ?>').multiselect('refresh');
			});
		}
		else
		{
			alert('<?php echo BB_JSSafe(BB_Translate("Warning:  Missing jQuery UI multiselect plugin for flat multiple selection field.\n\nThis feature requires AdminPack Extras.")); ?>');
		}
	});
	</script>
	<div style="clear: both;"></div>
<?php
					$state["extras_multiselect_flat"] .= ob_get_contents();
					ob_end_clean();

					$field["mode"] = "formhandler";
				}
			}
		}

		public static function MultiselectFinalize(&$state)
		{
			if ($state["extras_multiselect_tags"] !== false)  echo $state["extras_multiselect_tags"];
			if ($state["extras_multiselect_dropdown"] !== false)  echo $state["extras_multiselect_dropdown"];
			if ($state["extras_multiselect_flat"] !== false)  echo $state["extras_multiselect_flat"];
		}


		// Drag-and-drop reordering support for data tables.
		public static function TableOrderInit(&$state, &$options)
		{
			$state["extras_table_order"] = false;
		}

		public static function TableOrderTableRow(&$state, $num, &$field, $idbase, $type, $rownum, &$trattrs, &$colattrs, &$row)
		{
			global $bb_formtables;

			if ($bb_formtables && isset($field["order"]) && $field["order"] != "")
			{
				if ($type == "head")
				{
					$trattrs["id"] = $idbase . "_head";
					$trattrs["class"] .= " nodrag nodrop";
					array_unshift($row, BB_Translate($field["order"]));
					array_unshift($colattrs, array());

					// Queue up the necessary Javascript for later output.
					ob_start();
					if ($state["extras_table_order"] === false)
					{
?>
<script type="text/javascript" src="<?php echo htmlspecialchars($state["rooturl"] . "/" . $state["supportpath"] . "/jquery.tablednd-20140418.min.js"); ?>"></script>
<script type="text/javascript">
function AdminPackExtras_TableDnD_DefaultDrop(table, row) {
	var altrow = false;

	$(table).find('tr.row').each(function(x) {
		if (altrow)  $(this).addClass('altrow');
		else  $(this).removeClass('altrow');

		altrow = !altrow;
	});
}
</script>
<?php

						$state["extras_table_order"] = "";
					}

					$options = array(
						"dragHandle" => ".draghandle",
						"onDragClass" => "dragactive",
					);

					// Allow each tableDnD instance to be fully customized beyond basic support.
					// Valid options:  https://github.com/isocra/TableDnD
					if (isset($field["order_options"]))
					{
						foreach ($field["order_options"] as $key => $val)  $options[$key] = $val;
					}

?>
			<script type="text/javascript">
			if (jQuery.fn.tableDnD)
			{
				var options = <?php echo json_encode($options, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); ?>;
<?php
				if (!isset($field["order_callbacks"]))  $field["order_callbacks"] = array();

				// Legacy support for 'reordercallback'.
				if (isset($field["reordercallback"]))  $field["order_callbacks"]["onDrop"] = "function(table, row) { AdminPackExtras_TableDnD_DefaultDrop(table, row);  " .$field["reordercallback"]  . "(); }";
				else  $field["order_callbacks"]["onDrop"] = "AdminPackExtras_TableDnD_DefaultDrop";

				if (isset($field["order_callbacks"]))
				{
					foreach ($field["order_callbacks"] as $key => $val)
					{
?>
				options['<?php echo $key; ?>'] = <?php echo $val; ?>;
<?php
					}
				}
?>

				$('#<?php echo BB_JSSafe($idbase); ?>').tableDnD(options);
			}
			else
			{
				alert('<?php echo BB_JSSafe(BB_Translate("Warning:  Missing jQuery TableDnD plugin for drag-and-drop row ordering.\n\nThis feature requires AdminPack Extras.")); ?>');
			}
			</script>
<?php
					$state["extras_table_order"] .= ob_get_contents();
					ob_end_clean();
				}
				else if ($type == "body")
				{
					$trattrs["id"] = $idbase . "_" . $rownum;
					$colattrs[0]["class"] = "draghandle";
					array_unshift($row, "&nbsp;");
				}
			}
		}

		public static function TableOrderFinalize(&$state)
		{
			if ($state["extras_table_order"] !== false)  echo $state["extras_table_order"];
		}


		// Sticky headers for tables.
		public static function TableStickyHeadersInit(&$state, &$options)
		{
			$state["extras_table_stickyheaders"] = false;
		}

		public static function TableStickyHeadersTableRow(&$state, $num, &$field, $idbase, $type, $rownum, &$trattrs, &$colattrs, &$row)
		{
			global $bb_formtables;

			if ($bb_formtables && isset($field["stickyheader"]) && $field["stickyheader"])
			{
				if ($type == "head")
				{
					// Queue up the necessary Javascript for later output.
					ob_start();
					if ($state["extras_table_stickyheaders"] === false)
					{
?>
<script type="text/javascript" src="<?php echo htmlspecialchars($state["rooturl"] . "/" . $state["supportpath"] . "/jquery.stickytableheaders.min.js"); ?>"></script>
<script type="text/javascript">
$(window).resize(function() {
	$(window).trigger('resize.stickyTableHeaders');
});
</script>
<?php

						$state["extras_table_stickyheaders"] = "";
					}

					$options = array("__adminpack" => true);

					// Allow each sticky headers instance to be fully customized beyond basic support.
					// Valid options:  https://github.com/jmosbech/StickyTableHeaders
					if (isset($field["stickyheader_options"]))
					{
						foreach ($field["stickyheader_options"] as $key => $val)  $options[$key] = $val;
					}

?>
			<script type="text/javascript">
			if (jQuery.fn.stickyTableHeaders)
			{
				var options = <?php echo json_encode($options, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); ?>;
<?php
				if (!isset($field["order_callbacks"]))  $field["order_callbacks"] = array();

				if (isset($field["stickyheader_callbacks"]))
				{
					foreach ($field["stickyheader_callbacks"] as $key => $val)
					{
?>
				options['<?php echo $key; ?>'] = <?php echo $val; ?>;
<?php
					}
				}
?>

				$('#<?php echo BB_JSSafe($idbase); ?>').stickyTableHeaders(options);
			}
			else
			{
				alert('<?php echo BB_JSSafe(BB_Translate("Warning:  Missing jQuery Sticky Table Headers plugin.\n\nThis feature requires AdminPack Extras.")); ?>');
			}
			</script>
<?php
					$state["extras_table_stickyheaders"] .= ob_get_contents();
					ob_end_clean();
				}
			}
		}

		public static function TableStickyHeadersFinalize(&$state)
		{
			if ($state["extras_table_stickyheaders"] !== false)  echo $state["extras_table_stickyheaders"];
		}
	}


	// Register form handlers.
	if (function_exists("BB_RegisterPropertyFormHandler"))
	{
		BB_RegisterPropertyFormHandler("init", "AdminPackExtras::DateInit");
		BB_RegisterPropertyFormHandler("field_type", "AdminPackExtras::DateFieldType");
		BB_RegisterPropertyFormHandler("finalize", "AdminPackExtras::DateFinalize");

		BB_RegisterPropertyFormHandler("init", "AdminPackExtras::AccordionsInit");
		BB_RegisterPropertyFormHandler("field_string", "AdminPackExtras::AccordionsFieldString");
		BB_RegisterPropertyFormHandler("field_type", "AdminPackExtras::AccordionsCustomFieldType");
		BB_RegisterPropertyFormHandler("finalize", "AdminPackExtras::AccordionsFinalize");

		BB_RegisterPropertyFormHandler("init", "AdminPackExtras::MultiselectInit");
		BB_RegisterPropertyFormHandler("field_type", "AdminPackExtras::MultiselectFieldType");
		BB_RegisterPropertyFormHandler("finalize", "AdminPackExtras::MultiselectFinalize");

		BB_RegisterPropertyFormHandler("init", "AdminPackExtras::TableOrderInit");
		BB_RegisterPropertyFormHandler("table_row", "AdminPackExtras::TableOrderTableRow");
		BB_RegisterPropertyFormHandler("finalize", "AdminPackExtras::TableOrderFinalize");

		BB_RegisterPropertyFormHandler("init", "AdminPackExtras::TableStickyHeadersInit");
		BB_RegisterPropertyFormHandler("table_row", "AdminPackExtras::TableStickyHeadersTableRow");
		BB_RegisterPropertyFormHandler("finalize", "AdminPackExtras::TableStickyHeadersFinalize");
	}
?>