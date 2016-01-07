$(document).ready(function(){
		// Image: if image selected
	$(".htl-features-btn-more-ftr").on("click", function(e){
		e.preventDefault();
		addOtherFeatureFormFields();
	});
	// Other image div remove event
	$(document).on("click", ".wk_added_features_remove", function(){
		$(this).closest(".added_feature_div").remove();
	});

	function addOtherFeatureFormFields()
	{
		html = '<div class="added_feature_div"><div class="form-group"><label for="feature_title" class="control-label required"><span title="" data-toggle="tooltip" class="label-tooltip">'+feature_title_var+'</span></label><div class=""><input type="text" name="feature_title[]" class="form-control"><input type="hidden" name="feature_id[]" class="form-control" value="{$data.id}"></div></div>';
		
		html+='<div class="form-group"><label for="feature_description" class="control-label required"><span title="" data-toggle="tooltip" class="label-tooltip">'+feature_description_var+'</span></label><div class=""><input type="text" name="feature_description[]" class="form-control"></div></div>';
		
		html+='<div class="form-group"><label for="feature_image" class="control-label required col-sm-2"><span title="" data-toggle="tooltip" class="label-tooltip">'+feature_image_var+'"</span></label><div class="col-sm-8"><input type="file" name="feature_image[]"></div>';

		html+='<div class="col-sm-2"><a class="btn btn-default wk_added_features_remove pull-right"><span>'+remove_var+'</span></a></div></div><br><br><hr></div>';
	    $('.form_feature_div').append(html);
	}

	$(document).on("click", ".delete_htl_ftr", function(e){
		e.preventDefault();
		var id_row = $(this).data('id_row');
		var $current = $(this);

		$.ajax({
			url:module_dir+'wkhotelfeaturesblock/featureajaxprocess.php',
			data:{id_feature_row:id_row},
			method:'POST',
			type:'text',
			success:function(data)
			{
				if (data == 'success')
				{
					$current.closest(".feature_form_elements").remove();
					showSuccessMessage(remove_success_var);
				}
				else
				{
					alert(some_error_occur_cond);
				}
			}
		});
	});
});