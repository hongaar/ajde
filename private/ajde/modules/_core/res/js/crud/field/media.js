;
if (typeof AC ==="undefined") {AC = function(){}};
if (typeof AC.Crud ==="undefined") {AC.Crud = function(){}};
if (typeof AC.Crud.Edit ==="undefined") {AC.Crud.Edit = function(){}};

AC.Crud.Edit.Media = function() {
		
	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;

    /** FORM HANDLERS **/
    
    var onSave = function(data) {
        
    };

    var onBeforeSubmit = function() {
        
    };

    var onError = function() {
        
    };
    
    
        
	var $pointer;
	var $preview;
    var $typefield;
    var $thumbfield;
	var $filenamefield;
	
	var saveDir;
	var lastCode;

    var onImageUpload = function(e) {
		var preview, thumb;
        var filename = $(this).val();		
		var type = isImage(filename) ? 'image' : 'file';
		if (type === 'image') {
			preview = "<img src='" + saveDir + filename + "' />";
			thumb = filename;
		} else {
			preview = "<a href='" + saveDir + filename + "' target='_blank'>" + filename + "</a>";
			thumb = '';
		}
	
		if (!$filenamefield.val()) {
			$filenamefield.val(getFilename(filename));
		}
		
		update(filename, preview, type, thumb);
    };
	
	var isImage = function(filename) {
		var imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
		for (var i = 0; i < imageExtensions.length; i++) {
			if ( filename.toLowerCase().indexOf('.' + imageExtensions[i] ) === (filename.length - imageExtensions[i].length - 1) ) {
				return true;
			}
		}
		return false;
	};

	var getFilename = function(filename) {
		return filename.substr(0, filename.lastIndexOf('.'));
	};

    var embedChangeTimer;
    
    var onEmbedChange = function(e) {
        var self = this;
        clearTimeout(embedChangeTimer);
        embedChangeTimer = setTimeout(function() {
            var code = $(self).val();
			
			if (code && code !== lastCode) {
				lastCode = code;
				$('.preview').html('Looking up embed code or URL');			
				$.get('_core/component:embedInfo.json', { code: code }, function(data) {
					if (data.success === true) {
						update(data.code, data.code, 'embed', data.thumbnail);
					} else {
						update('', 'Embed code not recognized', 'unknown', '');
					}
				}, 'json');
			} else {
//				update('', '', 'unknown', '');
			}

        }, 300);
    };

    var onImageReset = function(e) {
        update('', '', 'unknown', '');
    };    
	
	var onReplaceMedia = function(e) {
		$('.uploadControls').removeClass('hidden');
		update('', '', 'unknown', '');
	};
	
	var update = function(pointer, preview, type, thumbnail) {
		$pointer.val(pointer);
		$preview.html(preview);
		$typefield.val(type);
		$thumbfield.val(thumbnail);
	};
    		
	return {
		
		init: function() {

            $('form.ACCrudEdit').data('onSave', onSave);
            $('form.ACCrudEdit').data('onError', onError);
            $('form.ACCrudEdit').data('onBeforeSubmit', onBeforeSubmit);
            
            $('form.ACCrudEdit input[name=_upload]').change(onImageUpload);
            $('form.ACCrudEdit input[name=_embed]').on('change keyup', onEmbedChange);
            $('form.ACCrudEdit input[name=_upload]').next('.ACAjaxUpload').bind('resetUpload', onImageReset);
			
			$('form.ACCrudEdit a.replaceMedia').on('click', onReplaceMedia);
                       
			$pointer = $('.media .hidden :input:eq(0)')
			$preview = $('.media .preview');
            $typefield = $(':input[name=' + $('.media').data('typefield') + ']');
            $thumbfield = $(':input[name=' + $('.media').data('thumbfield') + ']');
			$filenamefield = $(':input[name=' + $('.media').data('filenamefield') + ']');
			
			saveDir = $('.media').data('savedir');
			
			// Fancybox
			$('a.imagePreview').fancybox({closeBtn: false});
		}
		
	};
}();

$(document).ready(function() {
	AC.Crud.Edit.Media.init();
});