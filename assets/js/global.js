var editId = null, total=null;
$(function() {
	total = imports.length;
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
	$("#edit button.save").click(function() {
		$(".edit-fields input").each(function() {
			var id = $(this).prop("id");
			var val = $(this).val();
			imports[editId][id] = val;
		});
		$('#edit').modal('hide');
	});
	$("#import").click(function(e) {
		var count = 0;
		e.preventDefault();
		e.stopPropagation();

		$("#import").prop("disabled",true);
		$("tr td").css("background-color","");
		$(".progress-bar").css("width","");
		$(".progress").removeClass("hidden");
		$(".progress-bar").addClass("active");
		$.each(imports, function(i,v) {
			if(typeof v === "undefined") {
				return true;
			}
			//loop over and import indivudally.
			$.post( "ajax.php", {command: 'import', type: type, module: 'bulkhandler', imports: v},function( data ) {
				if(!data.status) {
					$("tr[data-unique-id=row-"+i+"] td").css("background-color","red");
					alert("There was an error importing row "+i+": "+data.message);
				} else {
					$("tr[data-unique-id=row-"+i+"] td").css("background-color","lightgreen");
				}
				count++

				$(".progress-bar").css("width",(count/total * 100) + "%");
				if(count == total) {
					$(".progress-bar").removeClass("active");
					$("#import").prop("disabled",false);
				}
			});
		});
	});
});
$("#validation-list").on("post-body.bs.table",function() {
	$(".actions i").click(function() {
		var type = $(this).data("type"), id = $(this).data("id"), jsonid = $(this).parents("tr").data("jsonid"), html = '';
		if(type == "delete") {
			$('table').bootstrapTable('remove', {field: 'id', values: [id.toString()]})
			delete(imports[jsonid]);
			total--;
		} else if(type == "edit" && typeof jsonid !== "undefined") {
			editId = jsonid;
			$.each(imports[jsonid], function(i,v) {
				var label = i;
				var input = '<input type="text" class="form-control" id="'+i+'" value="'+v+'">';

				if (headers && (header = headers[i])) {
					label = header['description'] ? header['description'] : i;

					if (!header['type'] || header['type'] == 'string') {
						if (header['values']) {
							input = '<select id="'+i+'" class="form-control">';
							$.each(header['values'], function(l) {
								value = header['values'][l];
								input = input + '<option value="'+l+'" '+(v==l?'selected':'')+'>'+value+'</option>';
							});
							input = input + '</select>';
						}
					} else if (header['type'] == 'destination') {
						/* TODO: Add destination dropdowns here. */
					}
				}
				html = html + '<div class="form-group"><label for="'+i+'">'+label+'</label>' + input + '</div>';
			});
			$("#edit .edit-fields").html(html);
			$('#edit').modal('show');
		}
	});
});
