jQuery(document).ready(function() {
	jQuery("#bdShortCode").click(function(){
		jQuery(this).focus();
		jQuery(this).select();
		document.execCommand("Copy");
		jQuery("#bdMsg").fadeIn(500).delay(3000).fadeOut(500);
	});
});