<div class="container-fluid">
	<div class="row">
		<div class="col-sm-12">
			<h1><?php echo _("Bulk Handler")?></h1>
				<ul class="nav nav-pills">
					<li role="presentation" class="active"><a href="?display=bulkhandler&amp;activity=export" class="list-group-item <?php echo ($activity == "export") ? 'active' : ''?>"><?php echo _('Export')?></a></li>
					<li role="presentation"><a href="?display=bulkhandler&amp;activity=import" class="list-group-item <?php echo ($activity == "import") ? 'active' : ''?>"><?php echo _('Import');?></a></li>
				</ul>
			<?php if(empty($types)) {?>
				<div class="alert alert-warning" role="alert"><?php echo _('No Bulk Exporters have been defined')?></div>
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
								<div class="panel-body collapse" id="<?php echo $key?>-moreinfo">
									<?php echo $type['description']?>
								</div>
							</div>
							<div class="container-fluid">
									<div class="element-container">
										<div class="row">
											<div class="col-md-12">
												<div class="row">
													<div class="form-group">
														<div class="col-md-3">
															<label class="control-label" for="<?php echo $key?>-export"><?php echo _('CSV File')?></label>
															<i class="fa fa-question-circle fpbx-help-icon" data-for="<?php echo $key?>-export"></i>
														</div>
														<div class="col-md-9"><a href="config.php?display=bulkhandler&amp;quietmode=1&amp;activity=export&amp;export=<?php echo $type['type']?>" target="_blank" class="btn" role="button"><?php echo _('Export')?></a></div>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-12">
												<span id="<?php echo $key?>-export-help" class="help-block fpbx-help-block"><?php echo _('Export to CSV')?></span>
											</div>
										</div>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
