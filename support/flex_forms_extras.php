<?php
	// FlexForms server-side extras.
	// (C) 2017 CubicleSoft.  All Rights Reserved.

	class FlexFormsExtras
	{
		// Date fields.
		public static function DateFieldType(&$state, $num, &$field, $id)
		{
			if ($field["type"] === "date")
			{
?>
			<input class="date"<?php if (isset($field["width"]))  echo " style=\"width: " . htmlspecialchars($field["width"]) . "\""; ?> type="text" id="<?php echo htmlspecialchars($id); ?>" name="<?php echo htmlspecialchars($field["name"]); ?>" value="<?php echo htmlspecialchars($field["value"]); ?>" />
<?php
				$state["jqueryuiused"] = true;

				$options = array("dateFormat" => "yy-mm-dd");

				// Allow the datepicker to be fully customized beyond basic support.
				// Valid options:  http://api.jqueryui.com/datepicker/
				if (isset($field["options"]))
				{
					foreach ($field["options"] as $key => $val)  $options[$key] = $val;
				}

				// Queue up the necessary Javascript for later output.
				ob_start();
?>
	jQuery(function() {
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

		if (jQuery.fn.datepicker)  jQuery('#<?php echo FlexForms::JSSafe($id); ?>').datepicker(options);
		else  alert('<?php echo FlexForms::JSSafe(FlexForms::FFTranslate("Warning:  Missing jQuery UI for date field.\n\nThis feature requires FlexForms Extras.")); ?>');
	});
<?php

				$state["js"]["date|" . $id] = array("mode" => "inline", "dependency" => "jqueryui", "src" => ob_get_contents());
				ob_end_clean();
			}
		}


		// Accordions.
		public static function AccordionsInit(&$state, &$options)
		{
			$state["extras_insideaccordion"] = false;

			$state["customfieldtypes"]["accordion"] = true;
			$state["customfieldtypes"]["accordian"] = true;
		}

		public static function AccordionsFieldString(&$state, $num, &$field, $id)
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

		public static function AccordionsCustomFieldType(&$state, $num, &$field, $id)
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
				<h3><?php echo htmlspecialchars(FlexForms::FFTranslate($field["title"])); ?></h3>
				<div class="formaccordionitems">
<?php
				}
				else
				{
					$id2 = $id . "_accordion";
?>
			<div class="formaccordionwrap" id="<?php echo htmlspecialchars($id2); ?>">
				<h3><?php echo htmlspecialchars(FlexForms::FFTranslate($field["title"])); ?></h3>
				<div class="formaccordionitems">
<?php
					$state["extras_insideaccordion"] = true;

					$state["jqueryuiused"] = true;

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

					// Queue up the necessary Javascript for later output.
					ob_start();
?>
	jQuery(function() {
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

		if (jQuery.fn.accordion)  jQuery('#<?php echo FlexForms::JSSafe($id2); ?>').accordion(options);
		else  alert('<?php echo FlexForms::JSSafe(FlexForms::FFTranslate("Warning:  Missing jQuery UI for accordion.\n\nThis feature requires FlexForms Extras.")); ?>');
	});
<?php

					$state["js"]["accordion|" . $id2] = array("mode" => "inline", "dependency" => "jqueryui", "src" => ob_get_contents());
					ob_end_clean();
				}

				$state["firstitem"] = true;
			}
		}

		public static function AccordionsCleanup(&$state)
		{
			if ($state["extras_insideaccordion"])
			{
?>
				</div>
			</div>
<?php

				$state["extras_insideaccordion"] = false;
			}
		}


		// Multiselect.
		public static function MultiselectInit(&$state, &$options)
		{
			if (!isset($state["extras_multiselect_tags"]))
			{
				$state["extras_multiselect_tags"] = false;
				$state["extras_multiselect_dropdown"] = false;
				$state["extras_multiselect_flat"] = false;
			}
		}

		public static function MultiselectFieldType(&$state, $num, &$field, $id)
		{
			if ($field["type"] == "select" && (isset($field["multiple"]) && $field["multiple"] === true) && isset($field["mode"]))
			{
				if ($field["mode"] == "tags")
				{
					if ($state["extras_multiselect_tags"] === false)
					{
						$state["css"]["multiselect-select2"] = array("mode" => "link", "dependency" => false, "src" => $state["supporturl"] . "/multiselect-select2/select2.css");
						$state["js"]["multiselect-select2"] = array("mode" => "src", "dependency" => "jquery", "src" => $state["supporturl"] . "/multiselect-select2/select2.min.js", "detect" => "jQuery.fn.select2");

						$state["extras_multiselect_tags"] = true;
					}

					$options = array("__flexforms" => true);
					if (isset($field["mininput"]))  $options["minimumInputLength"] = (int)$field["mininput"];

					// Allow each select2 instance to be fully customized beyond basic support.
					// Valid options:  https://select2.github.io/options.html
					if (isset($field["multiselect_options"]))
					{
						foreach ($field["multiselect_options"] as $key => $val)  $options[$key] = $val;
					}

					// Queue up the necessary Javascript for later output.
					ob_start();
?>
	jQuery(function() {
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

		if (jQuery.fn.select2)  jQuery('#<?php echo FlexForms::JSSafe($id); ?>').select2(options);
		else  alert('<?php echo FlexForms::JSSafe(FlexForms::FFTranslate("Warning:  Missing select2 for multiple selection field.\n\nThis feature requires FlexForms Extras.")); ?>');
	});
<?php
					$state["js"]["multiselect-select2|" . $id] = array("mode" => "inline", "dependency" => "multiselect-select2", "src" => ob_get_contents());
					ob_end_clean();

					$field["mode"] = "formhandler";
				}
				else if ($field["mode"] == "dropdown" && $state["extras_multiselect_flat"] === false)
				{
					if ($state["extras_multiselect_dropdown"] === false)
					{
						$state["jqueryuiused"] = true;

						$state["css"]["multiselect-widget"] = array("mode" => "link", "dependency" => "jqueryui", "src" => $state["supporturl"] . "/multiselect-widget/jquery.multiselect.css");
						$state["css"]["multiselect-widget-filter"] = array("mode" => "link", "dependency" => "multiselect-widget", "src" => $state["supporturl"] . "/multiselect-widget/jquery.multiselect.filter.css");
						$state["js"]["multiselect-widget"] = array("mode" => "src", "dependency" => "jqueryui", "src" => $state["supporturl"] . "/multiselect-widget/jquery.multiselect.min.js", "detect" => "jQuery.fn.multiselect");
						$state["js"]["multiselect-widget-filter"] = array("mode" => "src", "dependency" => "multiselect-widget", "src" => $state["supporturl"] . "/multiselect-widget/jquery.multiselect.filter.js", "detect" => "jQuery.fn.multiselectfilter");

						$state["extras_multiselect_dropdown"] = true;
					}

					$options = array(
						"selectedText" => FlexForms::FFTranslate("# of # selected"),
						"selectedList" => 5,
						"height" => (isset($field["height"]) ? (int)$field["height"] : 200),
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

					// Queue up the necessary Javascript for later output.
					ob_start();
?>
	jQuery(function() {
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

		if (jQuery.fn.multiselect && jQuery.fn.multiselectfilter)  jQuery('#<?php echo FlexForms::JSSafe($id); ?>').multiselect(options).multiselectfilter();
		else  alert('<?php echo FlexForms::JSSafe(FlexForms::FFTranslate("Warning:  Missing jQuery UI multiselect widget or multiselectfilter for dropdown multiple selection field.\n\nThis feature requires FlexForms Extras.")); ?>');
	});
<?php
					$state["js"]["multiselect-widget-filter|" . $id] = array("mode" => "inline", "dependency" => "multiselect-widget-filter", "src" => ob_get_contents());
					ob_end_clean();

					$field["mode"] = "formhandler";
				}
				else if ($field["mode"] == "flat" && $state["extras_multiselect_dropdown"] === false)
				{
					if ($state["extras_multiselect_flat"] === false)
					{
						$state["jqueryuiused"] = true;

						$state["css"]["multiselect-flat"] = array("mode" => "link", "dependency" => "jqueryui", "src" => $state["supporturl"] . "/multiselect-flat/css/jquery.uix.multiselect.css");
						$state["js"]["multiselect-flat"] = array("mode" => "src", "dependency" => "jqueryui", "src" => $state["supporturl"] . "/multiselect-flat/js/jquery.uix.multiselect.js", "detect" => "jQuery.fn.multiselect");

						$state["extras_multiselect_flat"] = true;
					}

					// Reorder selected options so that they appear in the correct order.
					if (isset($field["select"]))
					{
						if (is_string($field["select"]))  $field["select"] = array($field["select"] => true);

						$selected = $field["select"];
						foreach ($field["options"] as $name => $value)
						{
							if (is_array($value))
							{
								foreach ($value as $name2 => $value2)
								{
									if (isset($selected[$name2]))
									{
										$selected[$name2] = $value2;

										unset($value[$name2]);
									}
								}

								$field["options"][$name] = $value;
							}
							else if (isset($selected[$name]))
							{
								$selected[$name] = $value;

								unset($field["options"][$name]);
							}
						}

						$options = array();
						foreach ($selected as $name => $value)
						{
							if (is_string($value))  $options[$name] = $value;
						}
						foreach ($field["options"] as $name => $value)
						{
							$options[$name] = $value;
						}

						$field["options"] = $options;
					}

					$options = array(
						"availableListPosition" => ($state["formtables"] ? "left" : "top"),
						"sortable" => true,
						"sortMethod" => NULL
					);

					// Allow each multiselect instance to be fully customized beyond basic support.
					// Valid options:  https://github.com/yanickrochon/jquery.uix.multiselect/wiki/API-Documentation
					if (isset($field["multiselect_options"]))
					{
						foreach ($field["multiselect_options"] as $key => $val)  $options[$key] = $val;
					}

					// Queue up the necessary Javascript for later output.
					ob_start();
?>
	jQuery(function() {
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

			jQuery('#<?php echo FlexForms::JSSafe($id); ?>').multiselect(options);
			jQuery(window).resize(function() {
				jQuery('#<?php echo FlexForms::JSSafe($id); ?>').multiselect('refresh');
			});
		}
		else
		{
			alert('<?php echo FlexForms::JSSafe(FlexForms::FFTranslate("Warning:  Missing jQuery UI multiselect plugin for flat multiple selection field.\n\nThis feature requires FlexForms Extras.")); ?>');
		}
	});
<?php
					$state["js"]["multiselect-flat|" . $id] = array("mode" => "inline", "dependency" => "multiselect-flat", "src" => ob_get_contents());
					ob_end_clean();

					$field["mode"] = "formhandler";
				}
			}
		}


		// Drag-and-drop reordering support for data tables.
		public static function TableOrderInit(&$state, &$options)
		{
			if (!isset($state["extras_table_order"]))  $state["extras_table_order"] = false;
		}

		public static function TableOrderTableRow(&$state, $num, &$field, $idbase, $type, $rownum, &$trattrs, &$colattrs, &$row)
		{
			if ($state["formtables"] && isset($field["order"]) && $field["order"] != "")
			{
				if ($type == "head")
				{
					$trattrs["id"] = $idbase . "_head";
					$trattrs["class"] .= " nodrag nodrop";
					array_unshift($row, FlexForms::FFTranslate($field["order"]));
					array_unshift($colattrs, array());

					if ($state["extras_table_order"] === false)
					{
						$state["js"]["table-order"] = array("mode" => "src", "dependency" => "jquery", "src" => $state["supporturl"] . "/jquery.tablednd-20140418.min.js", "detect" => "jQuery.fn.tableDnD");

						ob_start();
?>
FlexForms.modules.Extras_TableDnD_DefaultDrop = function(table, row) {
	var altrow = false;

	jQuery(table).find('tr.row').each(function(x) {
		if (altrow)  jQuery(this).addClass('altrow');
		else  jQuery(this).removeClass('altrow');

		altrow = !altrow;
	});
}
<?php
						$state["js"]["table-order-defaultdrop"] = array("mode" => "inline", "dependency" => "table-order", "src" => ob_get_contents(), "detect" => "FlexForms.modules.Extras_TableDnD_DefaultDrop");
						ob_end_clean();

						$state["extras_table_order"] = true;
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

					// Queue up the necessary Javascript for later output.
					ob_start();
?>
	if (jQuery.fn.tableDnD)
	{
		var options = <?php echo json_encode($options, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES); ?>;
<?php
				if (!isset($field["order_callbacks"]))  $field["order_callbacks"] = array();

				// Legacy support for 'reordercallback'.
				if (isset($field["reordercallback"]))  $field["order_callbacks"]["onDrop"] = "function(table, row) { FlexForms.modules.Extras_TableDnD_DefaultDrop(table, row);  " .$field["reordercallback"]  . "(); }";
				else  $field["order_callbacks"]["onDrop"] = "FlexForms.modules.Extras_TableDnD_DefaultDrop";

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

		jQuery('#<?php echo FlexForms::JSSafe($idbase); ?>').tableDnD(options);
	}
	else
	{
		alert('<?php echo FlexForms::JSSafe(FlexForms::FFTranslate("Warning:  Missing jQuery TableDnD plugin for drag-and-drop row ordering.\n\nThis feature requires FlexForms Extras.")); ?>');
	}
<?php
					$state["js"]["table-order|" . $idbase] = array("mode" => "inline", "dependency" => "table-order-defaultdrop", "src" => ob_get_contents());
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


		// Sticky headers for tables.
		public static function TableStickyHeadersInit(&$state, &$options)
		{
			if (!isset($state["extras_table_stickyheaders"]))  $state["extras_table_stickyheaders"] = false;
		}

		public static function TableStickyHeadersTableRow(&$state, $num, &$field, $idbase, $type, $rownum, &$trattrs, &$colattrs, &$row)
		{
			if ($state["formtables"] && isset($field["stickyheader"]) && $field["stickyheader"])
			{
				if ($type == "head")
				{
					if ($state["extras_table_stickyheaders"] === false)
					{
						$state["js"]["table-sticky-headers"] = array("mode" => "src", "dependency" => "jquery", "src" => $state["supporturl"] . "/jquery.stickytableheaders.min.js", "detect" => "jQuery.fn.stickyTableHeaders");

						ob_start();
?>
FlexForms.modules.Extras_StickyTableHeadersResize = true;

jQuery(window).resize(function() {
	jQuery(window).trigger('resize.stickyTableHeaders');
});
<?php
						$state["js"]["table-sticky-headers-autoresize"] = array("mode" => "inline", "dependency" => "table-sticky-headers", "src" => ob_get_contents(), "detect" => "FlexForms.modules.Extras_StickyTableHeadersResize");
						ob_end_clean();

						$state["extras_table_stickyheaders"] = true;
					}

					$options = array("__flexforms" => true);

					// Allow each sticky headers instance to be fully customized beyond basic support.
					// Valid options:  https://github.com/jmosbech/StickyTableHeaders
					if (isset($field["stickyheader_options"]))
					{
						foreach ($field["stickyheader_options"] as $key => $val)  $options[$key] = $val;
					}

					// Queue up the necessary Javascript for later output.
					ob_start();
?>
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

		jQuery('#<?php echo FlexForms::JSSafe($idbase); ?>').stickyTableHeaders(options);
	}
	else
	{
		alert('<?php echo FlexForms::JSSafe(FlexForms::FFTranslate("Warning:  Missing jQuery Sticky Table Headers plugin.\n\nThis feature requires FlexForms Extras.")); ?>');
	}
<?php
					$state["js"]["table-sticky-headers|" . $idbase] = array("mode" => "inline", "dependency" => "table-sticky-headers", "src" => ob_get_contents());
					ob_end_clean();
				}
			}
		}
	}


	// Register form handlers.
	if (is_callable("FlexForms::RegisterFormHandler"))
	{
		FlexForms::RegisterFormHandler("field_type", "FlexFormsExtras::DateFieldType");

		FlexForms::RegisterFormHandler("init", "FlexFormsExtras::AccordionsInit");
		FlexForms::RegisterFormHandler("field_string", "FlexFormsExtras::AccordionsFieldString");
		FlexForms::RegisterFormHandler("field_type", "FlexFormsExtras::AccordionsCustomFieldType");
		FlexForms::RegisterFormHandler("cleanup", "FlexFormsExtras::AccordionsCleanup");

		FlexForms::RegisterFormHandler("init", "FlexFormsExtras::MultiselectInit");
		FlexForms::RegisterFormHandler("field_type", "FlexFormsExtras::MultiselectFieldType");

		FlexForms::RegisterFormHandler("init", "FlexFormsExtras::TableOrderInit");
		FlexForms::RegisterFormHandler("table_row", "FlexFormsExtras::TableOrderTableRow");

		FlexForms::RegisterFormHandler("init", "FlexFormsExtras::TableStickyHeadersInit");
		FlexForms::RegisterFormHandler("table_row", "FlexFormsExtras::TableStickyHeadersTableRow");
	}
?>