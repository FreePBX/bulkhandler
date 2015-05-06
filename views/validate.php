<h1>Data Validation</h1>
<table data-toggle="table" data-sort-name="stargazers_count" data-sort-order="desc" id="validation-list">
	<thead>
		<tr>
			<?php foreach ($headers as $key => $header) { ?>
				<?php if ($header['identifier']) { ?>
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
<div id="edit" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Modal title</h4>
      </div>
      <div class="modal-body">
				<div class="edit-fields">
				</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary save">Save changes</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>var type = "<?php echo $type?>"; var imports = <?php echo json_encode($imports)?>; var headers = <?php echo json_encode($headers)?>;</script>
<button type="button" id="submit">Submit</button>
