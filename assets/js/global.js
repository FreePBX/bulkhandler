$(function() {
	$("form.bulkhandler").submit(function() {
		if($(".importer:visible").val() == "") {
			alert(_("Not file specified"));
			$("#import").focus();
			return false;
		}
		if($(".importer:visible").length > 0 && $("#import").val().split('.').pop() != "csv") {
			alert(_('Only CSV files are supported'));
			$("#import").focus();
			return false;
		}
	});
});
