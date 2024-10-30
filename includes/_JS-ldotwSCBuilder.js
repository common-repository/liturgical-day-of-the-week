jQuery(document).ready(function() {
	jQuery('#bdShortCode').val('[liturgicaldotw]');
	jQuery('#ldotwSampleTitle').html("(Liturgical Day of the Week wording here)");
	jQuery('#ldotwSampleSaint').html("<br />(Saint of the Day wording here)");

	jQuery('.ldotwOtherDiocese').click(function() {
		var DioceseValue = jQuery('.ldotwOtherDiocese:checked').val();
		if(DioceseValue == 1){
			jQuery('#ldotwtextDiocese').val(' altdiocese="yes"');
		} else {
			jQuery('#ldotwtextDiocese').val('');
		}
		jQuery('#bdShortCode').val('[liturgicaldotw' + jQuery('#ldotwtextDiocese').val() + jQuery('#ldotwtextWording').val() + jQuery('#ldotwtextType').val()  + jQuery('#ldotwtextCWheel').val() + ']');
	});

	jQuery('.ldotwDWording').click(function() {
		var ldotwDWording = jQuery('.ldotwDWording:checked').val();
		if(ldotwDWording == "SaintWording") {
			jQuery('#ldotwtextWording').val(' format="saintwording"');
			jQuery('#ldotwSampleTitle').show();
			jQuery('#ldotwSampleSaint').show();
			jQuery('#ldotwSampleTitle').html("(Saint of the Day wording here)");
			jQuery('#ldotwSampleSaint').html("<br />(Liturgical Day of the Week wording here)");
		} else if(ldotwDWording == "ldwording") {
			jQuery('#ldotwtextWording').val(' format="ldwording"');
			jQuery('#ldotwSampleTitle').show();
			jQuery('#ldotwSampleSaint').hide();
			jQuery('#ldotwSampleType').hide();
		} else if(ldotwDWording == "saint") {
			jQuery('#ldotwtextWording').val(' format="saint"');
			jQuery('#ldotwSampleTitle').hide();
			jQuery('#ldotwSampleSaint').show();
			jQuery('#ldotwSampleType').hide();
		} else if(ldotwDWording == "none") {
			jQuery('#ldotwtextWording').val(' format="none"');
			jQuery('#ldotwSampleTitle').show();
			jQuery('#ldotwSampleSaint').hide();
			jQuery('#ldotwSampleTitle').html(" ");
			jQuery('#ldotwSampleType').hide();
		} else {
			jQuery('#ldotwtextWording').val('');
			jQuery('#ldotwSampleTitle').show();
			jQuery('#ldotwSampleSaint').show();
			jQuery('#ldotwSampleTitle').html("(Liturgical Day of the Week wording here)");
			jQuery('#ldotwSampleSaint').html("<br />(Saint of the Day wording here)");
		}
		jQuery('#bdShortCode').val('[liturgicaldotw' + jQuery('#ldotwtextDiocese').val() + jQuery('#ldotwtextWording').val() + jQuery('#ldotwtextType').val()  + jQuery('#ldotwtextCWheel').val() + ']');
	});

	jQuery('.ldotwfmdType').click(function() {
		var LCType = jQuery('.ldotwfmdType:checked').val();
		if(LCType == "none") {
			jQuery('#ldotwtextType').val(' fmslocation="none"');
			jQuery('#ldotwSampleType').hide();
		} else if(LCType == "wordingright") {
			jQuery('#ldotwtextType').val(' fmslocation="wordingright"');
			jQuery('#ldotwSampleType').show();
		} else if(LCType == "saintleft") {
			jQuery('#ldotwtextType').val(' fmslocation="saintleft"');
			jQuery('#ldotwSampleType').show();
		} else if(LCType == "saintright") {
			jQuery('#ldotwtextType').val(' fmslocation="saintright"');
			jQuery('#ldotwSampleType').show();
		} else {
			jQuery('#ldotwtextType').val('');
			jQuery('#ldotwSampleType').show();
		}
		jQuery('#bdShortCode').val('[liturgicaldotw' + jQuery('#ldotwtextDiocese').val() + jQuery('#ldotwtextWording').val() + jQuery('#ldotwtextType').val()  + jQuery('#ldotwtextCWheel').val() + ']');
	});

	jQuery('.ldotwcwheel').click(function() {
		var cWheelVal = jQuery('.ldotwcwheel:checked').val();
		if(cWheelVal == "hide") {
			jQuery('#ldotwtextCWheel').val(' addlcolors="hide"');
			jQuery('#ldotwSamplecwheel').hide();
		} else {
			jQuery('#ldotwtextCWheel').val('');
			jQuery('#ldotwSamplecwheel').show();
		}
		jQuery('#bdShortCode').val('[liturgicaldotw' + jQuery('#ldotwtextDiocese').val() + jQuery('#ldotwtextWording').val() + jQuery('#ldotwtextType').val()  + jQuery('#ldotwtextCWheel').val() + ']');
	});
});