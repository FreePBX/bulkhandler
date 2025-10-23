<?php
// Enforce UTF-8 on imports
foreach ($imports as $id => &$import) {
	foreach ($import as $key => &$value) {
		if (!mb_detect_encoding($value, 'UTF-8', true)) {
			$value = mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
		}
	}
	unset($value); //Break inner reference
}
unset($import); //Break outer reference

header('Content-Type: text/html; charset=utf-8');
?>
<div class="header-section">
	<h1 class="header"><?php echo _("Data Validation")?></h1>
	<div class="progress hidden">
		<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
			<span class="sr-only">0% <?php echo _("Complete") ?></span>
		</div>
	</div>
</div>

<div id="toolbar-all">
	<label><?php echo _("Replace/Update existing data")?></label>
	<div class="radioset" style="display: inline-block;">
		<input type="radio" name="replaceexisting" id="replaceexisting_yes" value="true" checked="">
		<label for="replaceexisting_yes"><?php echo _("Yes")?></label>
		<input type="radio" name="replaceexisting" id="replaceexisting_no" value="false">
		<label for="replaceexisting_no"><?php echo _("No")?></label>
	</div>
</div>

<div>
	<p id="error" style="color:red;"></p>
</div>

<table
	data-toggle="table"
	data-toolbar="#toolbar-all"
	data-show-columns="true"
	data-show-toggle="true"
	data-pagination="false"
	data-search="true"
	id="validation-list">
	<thead>
		<tr>
			<th data-field="id" class="id"><?php echo _('ID')?></th>
			<?php
				$identifiers = []; //Ensure initialized
				foreach ($headers as $key => $header) {
					if (isset($header['identifier']) && $header['identifier']) {
						$identifiers[] = $key;
						echo '<th data-field="' . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . '" data-sortable="true">' . htmlspecialchars($header['identifier'], ENT_QUOTES, 'UTF-8') . '</th>';
					}
				}
			?>
			<th data-field="actions" class="actions"><?php echo _('Actions')?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($imports as $id => $import) { ?>
			<tr class="scheme" data-unique-id="row-<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8')?>" data-jsonid='<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8')?>'>
				<td class="id"><?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8')?></td>
				<?php foreach ($identifiers as $identifier) { ?>
					<td data-value="<?php echo htmlspecialchars($identifier, ENT_QUOTES, 'UTF-8')?>"></td>
				<?php } ?>
				<td class="actions">
					<i class="fa fa-pencil-square-o actions clickable" data-type="edit" data-id="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8')?>"></i>
					<i class="fa fa-trash-o actions clickable" data-type="delete" data-id="<?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8')?>"></i>
				</td>
			</tr>
		<?php } ?>
	</tbody>
</table>

<br/><br/><br/>

<div id="edit" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><?php echo _('Edit')?></h4>
			</div>
			<div class="modal-body">
				<div class="edit-fields"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _('Close')?></button>
				<button type="button" class="btn btn-primary save"><?php echo _('Save changes')?></button>
			</div>
		</div>
	</div>
</div>

<script>
	var type = "<?php echo $type ?>";
	var imports = <?php echo json_encode($imports, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
	var identifiers = <?php echo json_encode($identifiers) ?>;

	$(document).ready(function () {
		Object.keys(imports).forEach(function (idx) {
			identifiers.forEach(function (identifier) {
				var cell = $(".scheme[data-jsonid='" + idx + "']").find("[data-value='" + identifier + "']");
				var raw = imports[idx][identifier] ?? "";

				// HTML-escape safely using jQuery
				var safeHtml = $('<div>').text(raw).html();
				cell.html(safeHtml);
			});
		});
	});
</script>
