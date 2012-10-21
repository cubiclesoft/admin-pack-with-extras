<?php
	// Admin Pack default mobile layout.
	// (C) 2012 CubicleSoft.  All Rights Reserved.

	// Disable features.
	$bb_formtables = false;
	$bb_formwidths = false;

	$bb_page_layout = <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>@TITLE@</title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
<link rel="stylesheet" href="@ROOTURL@/@SUPPORTPATH@/admin_mobile.css" type="text/css" media="all" />
<link rel="stylesheet" href="@ROOTURL@/@SUPPORTPATH@/admin_mobile_print.css" type="text/css" media="print" />
<script type="text/javascript" src="@ROOTURL@/@SUPPORTPATH@/jquery-1.7.2.min.js"></script>
</head>
<body>
<div class="pagewrap">
	<div class="headerwrap">@ROOTNAME@</div>
	<div class="contentwrap">
@MESSAGE@
<div class="maincontent">
@CONTENT@
</div>
	</div>
	<div class="menuwrap">@MENU@</div>
</div>
</body>
</html>
EOF;
?>