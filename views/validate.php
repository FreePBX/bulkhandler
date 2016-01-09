<div class="header-section">
	<h1 class="header"><?php echo _("Data Validation")?></h1>
	<div class="progress hidden">
		<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
			<span class="sr-only">0% Complete</span>
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
<table data-toggle="table"
				data-toolbar="#toolbar-all"
        data-show-columns="true"
        data-show-toggle="true"
        data-toggle="table"
        data-pagination="false"
        data-search="true"
				id="validation-list">
	<thead>
		<tr>
			<th data-field="id"><?php echo _('ID')?></th>
			<?php foreach ($headers as $key => $header) { ?>
				<?php if (isset($header['identifier']) && $header['identifier']) { ?>
					<?php $identifiers[] = $key;?>
					<th data-field="<?php echo $key?>" data-sortable="true"><?php echo $header['identifier']?></th>
				<?php } ?>
			<?php } ?>
			<th data-field="actions"><?php echo _('Actions')?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($imports as $id => $import) { ?>
			<tr class="scheme" data-unique-id="row-<?php echo $id?>" data-jsonid='<?php echo $id?>'>
				<td><?php echo $id?></td>
				<?php foreach ($identifiers as $identifier) { ?>

					<td><?php echo $import[$identifier]?></td>
				<?php } ?>
				<td class="actions">
					<i class="fa fa-pencil-square-o" data-type="edit" data-id="<?php echo $id?>"></i>
					<i class="fa fa-trash-o" data-type="delete" data-id="<?php echo $id?>"></i>
				</td>
			</tr>
		<?php } ?>
	</tbody>
</table>
<br/>
<br/>
<br/>
<div id="edit" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo _('Edit')?></h4>
      </div>
      <div class="modal-body">
				<div class="edit-fields">
				</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _('Close')?></button>
        <button type="button" class="btn btn-primary save"><?php echo _('Save changes')?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>var type = "<?php echo $type?>"; var imports = <?php echo json_encode($imports)?>; var headers = <?php echo json_encode($headers)?>;</script>
