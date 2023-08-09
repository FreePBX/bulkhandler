<div class="container-fluid">
	<div class="row">
		<div class="col-sm-12">
			<h1><?php echo _("Bulk Handler")?></h1>
				<ul class="nav nav-pills">
					<li role="presentation"><a href="?display=bulkhandler&amp;activity=export" class="list-group-item import__export__btn<?php echo ($activity == "export") ? 'active' : ''?>"><?php echo _('Export')?></a></li>
					<li role="presentation"  class="active"><a href="?display=bulkhandler&amp;activity=import" class="list-group-item import__export__btn <?php echo ($activity == "import") ? 'active' : ''?>"><?php echo _('Import');?></a></li>
				</ul>
			<?php if(empty($types)) {?>
				<div class="alert alert-warning" role="alert"><?php echo _('No Bulk Importers have been defined')?></div>
			<?php } else { ?>
				<?php if(!empty($message)) {?>
					<div class="alert alert-danger" role="alert"><?php echo $message?></div>
				<?php } ?>
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
							
								<form class="fpbx-submit bulkhandler" name="bulkhandler" action="config.php?display=bulkhandler&amp;activity=validate" method="post" role="form" enctype="multipart/form-data">
									<input type="hidden" name="type" value="<?php echo $type['type']?>">
									<div class="container-fluid">
									
									<?php //lets do check for custom fields
										$modupcase = ucfirst((string) $type['type']);
										if(isset($customfields[$modupcase]) && is_array($customfields[$modupcase])){
											foreach($customfields as $mod => $fields) {
												if($mod) {
													foreach($fields as $fieldname => $fieldval){ 
													if(!in_array('import',$fieldval['activity'])){//remove the unwanted custom paramters based on activity
															continue;
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
																				$selected??='';
																				if(isset($_REQUEST[$fieldname])){
																					if($val['id'] == $_REQUEST[$fieldname] ){
																						$selected = 'selected';
																					} else {
																						$selected ='';
																					}
																				}
																			echo '<option value="'. $val[$fieldval['valuetopass']] .'" '.$selected.' >'. $val[$fieldval['valuetodisplay']].'</option>';
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
																	<label class="control-label" for="<?php echo $key?>-import"><?php echo _('CSV File')?></label>
																	<i class="fa fa-question-circle fpbx-help-icon" data-for="<?php echo $key?>-import"></i>
																</div>
																<div class="col-md-9">
																	<span class="btn btn-default btn-file"><?php echo _("Browse")?> <input type="file" class="form-control importer" name="import"></span>
																	<span class="filename"></span>
																</div>
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
														<div class="">
															<div class="row form-group">
																<div class="col-md-3">
																	<label class="control-label" for="<?php echo $key?>-headers"><?php echo _('Required(*)/Recommended Headers')?></label>
																	<i class="fa fa-question-circle fpbx-help-icon" data-for="<?php echo $key?>-headers"></i>
																</div>
																<div class="col-md-9">
																	<pre><?php
																		foreach($type['headers'] as $key1 => $header) {
																			$mark = Null;
																			if(isset($header['required']) && $header['required'] == 1){
																				$mark = "*";
																			}
																			echo $key1.($header['description'] ? " (".$mark. $header['description'] . ")" : "") . ",\n";
																		}
																	?></pre>
																</div>
															</div>
														</div>
													</div>
												</div>
												<div class="row">
													<div class="col-md-12">
														<span id="<?php echo $key?>-headers-help" class="help-block fpbx-help-block"><?php echo _('Required headers(*) are the minimum headers you must supply to be able to import. For a full list of all supported headers export out a CSV file from the export display')?></span>
													</div>
												</div>
											</div>
											<div class="element-container">
												<div class="row">
													<div class="col-md-12">
														<div class="">
															<div class="row form-group">
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
	</div>
</div>
