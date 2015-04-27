$(function() {
	$("form[name=bulkhandler]").submit(function() {
		if($("#import").val() == "") {
			alert(_("Not file specified"));
			$("#import").focus();
			return false;
		}
		if($("#import").length > 0 && $("#import").val().split('.').pop() != "csv") {
			alert(_('Only CSV files are supported'));
			$("#import").focus();
			return false;
		}
	});
});
