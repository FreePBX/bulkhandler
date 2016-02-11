var editId = null, total=null, tabDisplay = $.cookie("bulkhandler-display");
if(typeof tabDisplay !== "undefined") {
	$('[aria-controls='+tabDisplay+']').tab('show');
}

$(function() {
	$('.nav-tabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		var name = $(e.target).parents("li").data("name");
		$.cookie("bulkhandler-display",name);
	});
	total = (typeof imports !== "undefined") ? imports.length : 0;
	$("form.bulkhandler").submit(function() {
		if($(".importer:visible").val() === "") {
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
	$("#cancel").click(function(e) {
		e.preventDefault();
		e.stopPropagation();
		window.location = '?display=bulkhandler&activity=import';
	});
	$("#import").click(function(e) {
		var count = 0, errors = 0, replace = $("#replaceexisting_yes").is(":checked");
		e.preventDefault();
		e.stopPropagation();

		$("#import").prop("disabled",true);
		$("tr td").css("background-color","");
		$(".progress-bar").css("width","");
		$(".progress").removeClass("hidden");
		$(".progress-bar").addClass("active");
		if(total === 0) {
			alert(_("There is nothing to import!"));
			return;
		}
		async.forEachOfSeries(imports, function (v, i, callback) {
			if(typeof v === "undefined") {
				callback();
				return;
			}
			$.post( "ajax.php", {command: 'import', type: type, module: 'bulkhandler', imports: v, replace: (replace ? 1 : 0)},function( data ) {
				if(!data.status) {
					$("tr[data-unique-id=row-"+i+"] td").css("background-color","red");
					alert(sprintf(_("There was an error importing row %s: %s"),i,data.message));
					errors++;
				} else {
					$("tr[data-unique-id=row-"+i+"] td").css("background-color","lightgreen");
				}
				count++;

				$(".progress-bar").css("width",(count/total * 100) + "%");
				if(count == total) {
					$(".progress-bar").removeClass("active");
					$("#import").prop("disabled",false);
				}
				callback();
			});
		}, function (err) {
			if(errors === 0) {
				$("#import").prop("value",_("Reimport"));
				$("#cancel").prop("value",_("Finished"));
			}
		});
	});
});
$("#validation-list").on("post-body.bs.table",function() {
	$(".actions i").click(function() {
		var type = $(this).data("type"), id = $(this).data("id"), jsonid = $(this).parents("tr").data("jsonid"), html = '', destid = 0;
		if(type == "delete") {
			$('table').bootstrapTable('remove', {field: 'id', values: [id.toString()]})
			delete(imports[jsonid]);
			total--;
		} else if(type == "edit" && typeof jsonid !== "undefined") {
			editId = jsonid;
			$.each(imports[jsonid], function(i,v) {
				var label = i;
				var input = '<input type="text" class="form-control" id="'+i+'" value=\''+v+'\'>';

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
						/* TODO: Add destination dropdowns here.
						/* Problem is that every destination can be slightly different than
						/* the previous one if using custom. Forgo this for now
						input = "<div id='dest-"+destid+"' class='destination-loading'>"+_("Loading")+"</div>";
						$.post( "ajax.php", {module: "bulkhandler", command: "destinationdrawselect", id: i, value: v, destid: destid}, function( data ) {
							$("#dest-"+data.destid).html(data.html);
						});
						destid++;
						*/
						input = '<input type="text" class="form-control" id="'+i+'" value=\''+v+'\'>';
					}
				}
				html = html + '<div class="form-group"><label for="'+i+'">'+label+'</label>' + input + '</div>';
			});
			$("#edit .edit-fields").html(html);
			$('#edit').modal('show');
		}
	});
});

function htmlEntities(str) {
	return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function addslashes(str) {
  //  discuss at: http://phpjs.org/functions/addslashes/
  // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Ates Goral (http://magnetiq.com)
  // improved by: marrtins
  // improved by: Nate
  // improved by: Onno Marsman
  // improved by: Brett Zamir (http://brett-zamir.me)
  // improved by: Oskar Larsson Högfeldt (http://oskar-lh.name/)
  //    input by: Denny Wardhana
  //   example 1: addslashes("kevin's birthday");
  //   returns 1: "kevin\\'s birthday"

  return (str + '')
    .replace(/[\\"']/g, '\\$&')
    .replace(/\u0000/g, '\\0');
}
