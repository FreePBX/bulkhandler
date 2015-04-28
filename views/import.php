<div class="container-fluid">
	<div class="row">
		<div class="col-sm-9">
			<div class="fpbx-container">
				<form class="fpbx-submit" name="bulkhandler" action="config.php?display=bulkhandler" method="post" role="form" enctype="multipart/form-data">
					<input type="hidden" name="type" value="extensions">
					<ul class="nav nav-tabs" role="tablist">
						<?php foreach($types as $key => $type) {?>
							<li data-name="<?php echo $key?>" class="change-tab active"><a href="#<?php echo $key?>" aria-controls="<?php echo $key?>" role="tab" data-toggle="tab"><?php echo $type['name']?></a></li>
						<?php } ?>
					</ul>
					<div class="tab-content display">
						<?php foreach($types as $key => $type) {?>
						<div id="<?php echo $key?>" class="tab-pane active">
							<div class="container-fluid">
									<div class="element-container">
										<div class="row">
											<div class="col-md-12">
												<div class="row">
													<div class="form-group">
														<div class="col-md-3">
															<label class="control-label" for="import"><?php echo _('CSV File')?></label>
															<i class="fa fa-question-circle fpbx-help-icon" data-for="import"></i>
														</div>
														<div class="col-md-9"><input name="import" type="file" class="form-control" id="import"></div>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12">
												<span id="import-help" class="help-block fpbx-help-block"><?php echo _('File to Import')?></span>
											</div>
										</div>
									</div>
									<div class="element-container">
										<div class="row">
											<div class="col-md-12">
												<div class="row">
													<div class="form-group">
														<div class="col-md-3">
															<label class="control-label" for="headers"><?php echo _('Supported Headers')?></label>
															<i class="fa fa-question-circle fpbx-help-icon" data-for="headers"></i>
														</div>
														<div class="col-md-9"><pre><?php echo print_r($headers);?></pre></div>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12">
												<span id="headers-help" class="help-block fpbx-help-block"><?php echo _('Supported Headers in the CSV File')?></span>
											</div>
										</div>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
				</form>
			</div>
		</div>
		<div class="col-sm-3 hidden-xs bootnav">
			<div class="list-group">
				<a href="?display=bulkhandler&amp;type=import" class="list-group-item <?php echo ($typed == "import") ? 'active' : ''?>"><?php echo _('Import');?></a>
				<a href="?display=bulkhandler&amp;type=export" class="list-group-item <?php echo ($typed == "export") ? 'active' : ''?>"><?php echo _('Export')?></a>
			</div>
		</div>
	</div>
</div>
