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
});