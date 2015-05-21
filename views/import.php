<div class="container-fluid">
	<div class="row">
		<div class="col-sm-9">
			<?php if(empty($types)) {?>
				<div class="alert alert-warning" role="alert"><?php echo _('No Bulk Importers have been defined')?></div>
			<?php } else { ?>
				<div class="fpbx-container">
						<ul class="nav nav-tabs" role="tablist">
							<?php foreach($types as $key => $type) {?>
								<li data-name="<?php echo $key?>" class="change-tab <?php echo $type['active'] ? 'active' : ''?>"><a href="#<?php echo $key?>" aria-controls="<?php echo $key?>" role="tab" data-toggle="tab"><?php echo $type['name']?></a></li>
							<?php } ?>
						</ul>
						<div class="tab-content display">
							<?php foreach($types as $key => $type) {?>
							<div id="<?php echo $key?>" class="tab-pane <?php echo $type['active'] ? 'active' : ''?>">
								<div class="panel panel-info">
									<div class="panel-heading">
										<div class="panel-title">
											<a href="#" data-toggle="collapse" data-target="#<?php echo $key?>-moreinfo"><i class="glyphicon glyphicon-info-sign"></i></a>&nbsp;&nbsp;&nbsp;<?php echo sprintf(_('What are "%s"?'),$type['type'])?></div>
									</div>
									<!--At some point we can probably kill this... Maybe make is a 1 time panel that may be dismissed-->
									<div class="panel-body collapse" id="<?php echo $key?>-moreinfo">
										<?php echo $type['description']?>
									</div>
								</div>
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
																<div class="col-md-9">
																	<pre><?php
																		foreach($type['headers'] as $key1 => $header) {
																			echo $key1 . ($header['description'] ? " (" . $header['description'] . ")" : "") . ",\n";
																		}
																	?></pre>
																</div>
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
			<?php } ?>
		</div>
		<div class="col-sm-3 hidden-xs bootnav">
			<div class="list-group">
				<a href="?display=bulkhandler&amp;activity=export" class="list-group-item <?php echo ($activity == "export") ? 'active' : ''?>"><?php echo _('Export')?></a>
				<a href="?display=bulkhandler&amp;activity=import" class="list-group-item <?php echo ($activity == "import") ? 'active' : ''?>"><?php echo _('Import');?></a>
			</div>
		</div>
	</div>
</div>
