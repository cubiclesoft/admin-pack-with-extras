function InitPropertiesTableDragAndDrop(tableid, callback)
{
	$('#' + tableid).tableDnD({
		dragHandle : '.draghandle',
		onDragClass : 'dragactive',
		onDrop : function (table, row) {
			var altrow = false;

			$('#' + tableid + ' tr.row').each(function(x) {
				if (altrow)  $(this).addClass('altrow');
				else  $(this).removeClass('altrow');

				altrow = !altrow;
			});

			if (callback)  callback();
		}
	});
}

$(window).resize(function() {
	$(window).trigger('resize.stickyTableHeaders');
});

$(function() {
	$('#navbutton').click(function() {
		$('#navbutton').toggleClass("clicked");
		$('#navdropdown').toggle().each(function() {
			pos = $('#navbutton').position();
			height = $('#navbutton').outerHeight();
			$(this).css({ top: (pos.top + height) + "px" });
		});
	});

	$('.leftnav').clone().appendTo('#navdropdown');

	$('input.nopasswordmanager[type=password]').each(function() {
		$(this).attr('data-background-color', $(this).css('background-color'));
		$(this).css('background-color', $(this).css('color'));
		$(this).attr('type', 'text');

		$(this).focus(function() {
			$(this).attr('type', 'password');
			$(this).css('background-color', $(this).attr('data-background-color'));
		});

		$(this).blur(function() {
			$(this).css('background-color', $(this).css('color'));
			$(this).attr('type', 'text');
		});
	});
});