$(document).ready(function(){
	// Image: if image selected
	$(".htl-testimonial-btn-more-testimonials").on("click", function(e){
		e.preventDefault();
		addOtherTestimonialFormFields();
	});
	// Other image div remove event
	$(document).on("click", ".wk_added_testimonials_remove", function(){
		$(this).closest(".added_testimonial_div").remove();
	});

	if ($(".delete_htl_testimonial").data('id_row') == '')
	{
		$(".delete_htl_testimonial").hide();
	}

	function addOtherTestimonialFormFields()
	{
		html = '<div class="added_testimonial_div"><div class="form-group"><label for="name" class="control-label"><span title="" data-toggle="tooltip" class="label-tooltip">'+name_var+'</span></label><div class=""><input type="text" name="name[]" class="form-control" value=""><input type="hidden" name="testimonial_id[]" class="form-control"></div></div>';

		html+='<div class="form-group"><label for="testimonial_content" class="control-label required"><span title="" data-toggle="tooltip" class="label-tooltip">'+testimonial_content_var+'</span></label><div class=""><input type="text" name="testimonial_content[]" class="form-control wk_tinymce"></div></div>';
		
		html+='<div class="form-group"><label for="testimonial_image" class="control-label col-sm-2"><span title="" data-toggle="tooltip" class="label-tooltip">'+person_image_var+'"</span></label><div class="col-sm-8"><input type="file" name="testimonial_image[]" value=""></div>';

		html+='<div class="col-sm-2"><a class="btn btn-default wk_added_testimonials_remove pull-right"><span>'+remove_var+'</span></a></div></div><br><br><hr></div>';
	    $('.form_testimonial_div').append(html);
	}

	$(document).on("click", ".delete_htl_testimonial", function(e){
		e.preventDefault();
		var id_row = $(this).data('id_row');
		var $current = $(this);

		$.ajax({
			url:module_dir+'wktestimonialblock/testimonialajaxprocess.php',
			data:{id_testimonial_row:id_row},
			method:'POST',
			type:'text',
			success:function(data)
			{
				if (data == 'success')
				{
					$current.closest(".testimonial_form_elements").remove();
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