<div class="container-fluid">
	<div class="row">
		<div class="col-sm-12">
			<h1><?php echo _("Bulk Handler")?></h1>
				<ul class="nav nav-pills">
					<li role="presentation" class="active"><a href="?display=bulkhandler&amp;activity=export" class="list-group-item import__export__btn <?php echo ($activity == "export") ? 'active' : ''?>"><?php echo _('Export')?></a></li>
					<li role="presentation"><a href="?display=bulkhandler&amp;activity=import" class="list-group-item import__export__btn <?php echo ($activity == "import") ? 'active' : ''?>"><?php echo _('Import');?></a></li>
				</ul>
			<?php if(empty($types)) {?>
				<div class="alert alert-warning" role="alert"><?php echo _('No Bulk Exporters have been defined')?></div>
			<?php } else { ?>
				<div class="fpbx-container">
					<ul class="nav nav-tabs pb-0" role="tablist">
						<?php foreach($types as $key => $type) {?>
							<li data-name="<?php echo $key?>" class="change-tab"><a class="nav-link <?php echo $type['active'] ? 'active' : ''?>" href="#<?php echo $key?>" aria-controls="<?php echo $key?>" role="tab" data-toggle="tab"><?php echo $type['name']?></a></li>
						<?php } ?>
					</ul>
					<div class="tab-content display">
						<?php foreach($types as $key => $type) {?>
							<div id="<?php echo $key?>" class="tab-pane <?php echo $type['active'] ? 'active' : ''?>">
							
							<!--At some point we can probably kill this... Maybe make is a 1 time panel that may be dismissed-->
							<?php echo show_help( $type['description'], sprintf(_('What are "%s"?'),$type['type']), false, true, "info"); ?>
							
							<div class="container-fluid">
							<form class="fpbx-submit bulkhandler" name="bulkhandlerexport" action="config.php?display=bulkhandler&amp;quietmode=1&amp;activity=export&amp;export=<?php echo $type['type']?>" method="post" role="form" >
							<?php //lets do check for custom fields
										$modupcase = ucfirst((string) $type['type']);
										if(isset($customfields[$modupcase]) && is_array($customfields[$modupcase])){
											foreach($customfields as $mod => $fields) {
												if($mod) {
													foreach($fields as $fieldname => $fieldval){
														if(is_array($fieldval['activity'])){
															if(!in_array('export',$fieldval['activity'])){//remove the unwanted custom paramters based on activity
																continue;
															}
														}
											?>
											<div class="element-container">
												<div class="row">
													<div class="col-md-12">
														<div class="">
															<div class="row form-group">
																<div class="col-md-3">
																	<label class="control-label" for="<?php echo $fieldname?>-import"><?php echo _($fieldval['label'])?></label>
																	<i class="fa fa-question-circle fpbx-help-icon" data-for="<?php echo $fieldname?>-import"></i>
																</div>
																<?php 
																foreach($fieldval as $ftype => $fields){
																	if($ftype =='SELECT') { ?>
																		<div class="col-md-9">
																		<select class="form-control" name="<?php echo $fieldname?>"> 
																		<?php foreach($fields as $val) {
																			echo '<option value='. $val[$fieldval['valuetopass']] .'>'. $val[$fieldval['valuetodisplay']].'</option>';
																		}?>
																		</select>
																		</div>
																<?php
																	}
																}
																?>
																
															</div>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-12">
														<span id="<?php echo $fieldname?>-import-help" class="help-block fpbx-help-block"><?php echo _($fieldval['desc'])?></span>
													</div>
												</div>
											</div>
													<?php
													}
												}
											
											}
										}
									?>
									<div class="element-container">
										<div class="row">
											<div class="col-md-12">
												<div class="">
													<div class="row form-group">
														<div class="col-md-3">
															<label class="control-label" for="<?php echo $key?>-export"><?php echo _('CSV File')?></label>
															<i class="fa fa-question-circle fpbx-help-icon" data-for="<?php echo $key?>-export"></i>
														</div>
														<div class="col-md-9"><button type="submit" formtarget="_blank" class="btn" role="button"><?php echo _('Export')?> </button></div>
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
								</form>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
