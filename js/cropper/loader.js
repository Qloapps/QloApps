var CropImageManager = {
	curCrop: null,
	
	init: function()
	{
		this.attachCropper();
	},

	onChange: function(e)
	{
		var vals = $F(Event.element(e)).split('|');
		this.setImage(vals[0], vals[1], vals[2]);
	},
	
	setImage: function(imgSrc, w, h)
	{
		$('testImage').src = imgSrc;
		/*$('testImage').width = w;
		$('testImage').height = h;*/
		this.attachCropper(w, h);
	},

	attachCropper: function(maxW, maxH)
	{
		var vals = $F($('imageChoice')).split('|');
		if (!maxW)
			maxW = vals[1];
		if (!maxH)
			maxH = vals[2];
		if (this.curCrop == null)
			this.curCrop = new Cropper.Img('testImage',
				{
					minWidth: maxW,
					minHeight: maxH,
					maxWidth: maxW,
					maxHeight: maxH,
					onEndCrop: onEndCrop
				}
			);
		else
			this.curCrop.reset(maxW, maxH, maxW, maxH);
		this.curCrop.aeraCoords = 0;
	},
	
	removeCropper: function()
	{
		if (this.curCrop != null)
			this.curCrop.remove();
	},
	
	resetCropper: function()
	{
		this.attachCropper();
	}
};

function onEndCrop(coords, dimensions)
{
	var vals = $F($('imageChoice')).split('|');
	var id_image = vals[3];
	if (!image)
	{
		image = id_image;
		image_check = id_image;
	}
	if (image != id_image)
		image = id_image;
	else
	{
		if (image != image_check && navigator.appName != "Microsoft Internet Explorer")
			image_check = image;
		else
		{
			$(id_image + '_x1').value = coords.x1;
			$(id_image + '_y1').value = coords.y1;
			$(id_image + '_x2').value = coords.x2;
			$(id_image + '_y2').value = coords.y2;
		}
	}
}

Event.observe(window, 'load',
	function() {
		CropImageManager.init();
		Event.observe($('imageChoice'), 'change', CropImageManager.onChange.bindAsEventListener(CropImageManager), false );
	}
);

var image;
var image_check;