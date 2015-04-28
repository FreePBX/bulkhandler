<div class="container-fluid">
	<div class="row">
		<div class="col-sm-9">
			<div class="fpbx-container">
					<ul class="nav nav-tabs" role="tablist">
						<?php foreach($types as $key => $type) {?>
							<li data-name="<?php echo $key?>" class="change-tab <?php echo $type['active'] ? 'active' : ''?>"><a href="#<?php echo $key?>" aria-controls="<?php echo $key?>" role="tab" data-toggle="tab"><?php echo $type['name']?></a></li>
						<?php } ?>
					</ul>
					<div class="tab-content display">
						<?php foreach($types as $key => $type) {?>
						<div id="<?php echo $key?>" class="tab-pane <?php echo $type['active'] ? 'active' : ''?>">
							<form class="fpbx-submit bulkhandler" name="bulkhandler" action="config.php?display=bulkhandler" method="post" role="form" enctype="multipart/form-data">
								<input type="hidden" name="type" value="<?php echo $type['type']?>">
								<div class="container-fluid">
										<div class="element-container">
											<div class="row">
												<div class="col-md-12">
													<div class="row">
														<div class="form-group">
															<div class="col-md-3">
																<label class="control-label" for="<?php echo $key?>-import"><?php echo _('CSV File')?></label>
																<i class="fa fa-question-circle fpbx-help-icon" data-for="<?php echo $key?>-import"></i>
															</div>
															<div class="col-md-9"><input name="import" type="file" class="form-control importer"></div>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<span id="<?php echo $key?>-import-help" class="help-block fpbx-help-block"><?php echo _('File to Import')?></span>
												</div>
											</div>
										</div>
										<div class="element-container">
											<div class="row">
												<div class="col-md-12">
													<div class="row">
														<div class="form-group">
															<div class="col-md-3">
																<label class="control-label" for="<?php echo $key?>-headers"><?php echo _('Supported Headers')?></label>
																<i class="fa fa-question-circle fpbx-help-icon" data-for="<?php echo $key?>-headers"></i>
															</div>
															<div class="col-md-9"><pre><?php echo print_r($headers);?></pre></div>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<span id="<?php echo $key?>-headers-help" class="help-block fpbx-help-block"><?php echo _('Supported Headers in the CSV File')?></span>
												</div>
											</div>
										</div>
										<div class="element-container">
											<div class="row">
												<div class="col-md-12">
													<div class="row">
														<div class="form-group">
															<div class="col-md-3"></div>
															<div class="col-md-9"><input type="submit" value="<?php echo _('Submit')?>"></div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</form>
							</div>
						<?php } ?>
					</div>
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
