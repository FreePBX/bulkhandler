var editId = null;
$(function() {
	$("form.bulkhandler").submit(function() {
		if($(".importer:visible").val() == "") {
			alert(_("Not file specified"));
			$(".importer:visible").focus();
			return false;
		}
		if($(".importer:visible").length > 0 && $(".importer:visible").val().split('.').pop() != "csv") {
			alert(_('Only CSV files are supported'));
			$(".importer:visible").focus();
			return false;
		}
	});
});
$("#validation-list").on("post-body.bs.table",function() {
	$(".actions i").click(function() {
		var type = $(this).data("type"), id = $(this).data("id"), jsonid = $(this).parents("tr").data("jsonid"), html = '';
		if(type == "delete") {
			$('table').bootstrapTable('hideRow', {index: 'row-'+id,isIdField: true});
			delete(imports[jsonid]);
		} else if(type == "edit" && typeof jsonid !== "undefined") {
			editId = jsonid;
			$.each(imports[jsonid], function(i,v) {
				html = html + '<div class="form-group"><label for="'+i+'">'+i+'</label><input type="text" class="form-control" id="'+i+'" value="'+v+'"></div>';
			});
			$("#edit .edit-fields").html(html);
			$('#edit').modal('show');
		}
	});
});
$("#edit button.save").click(function() {
	$(".edit-fields input").each(function() {
		var id = $(this).prop("id");
		var val = $(this).val();
		imports[editId][id] = val;
	});
	$('#edit').modal('hide');
});
$("#submit").click(function() {
	$.each(imports, function(i,v) {
		//loop over and import indivudally
		$.post( "ajax.php", {command: 'import', type: type, module: 'bulkhandler', imports: v},function( data ) {
			if(!data.status) {
				$("tr[data-uniqueid=row-"+i+"] td").css("background-color","red");
				alert("There was an error importing row "+i+": "+data.message);
			} else {
				$("tr[data-uniqueid=row-"+i+"] td").css("background-color","lightgreen");
			}
		});
	});
});
