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
    
    
    

    var onImageUpload = function(e) {
        var filename = $(this).val();
        $img = $('<img/>').attr('src', 'public/images/content/' + filename);
        $('div.imagePreview').append($img);
        setThumbnail('public/images/content/' + filename, false, filename);
    };

    var embedChangeTimer;
    
    var $typefield;
    var $thumbfield;
    
    var onEmbedChange = function(e) {
        var self = this;
        clearTimeout(embedChangeTimer);
        embedChangeTimer = setTimeout(function() {
            var code = $(self).val();

            $.get('_core/component:embedInfo.json', { code: code }, function(data) {
                if (data.success === true) {
                    $typefield.val('embed');
                    $('.preview').html(data.code);
                    if (data.thumbnail) {
                        $thumbfield.val(data.thumbnail);
                    } else {
                        $thumbfield.val('');
                    }					
                } else {
                    $('.preview').html('Embed code not recognized');
                    $thumbfield.val('');
                    $typefield.val('unknown');
                }
            }, 'json');

        }, 300);
    };

    var onImageReset = function(e) {
        $('div.imagePreview').empty();		
        $('input[name=image]').val('');
        onThumbnailReset();
    };
    
     // init images on edit
    var emulateUploadComplete = function(name, filename) {
        var $input = $('form.ACCrudEdit input[name=' + name + ']');
        var elm = $input.next('div');
        $input.val(filename).change();
        elm.after($('<span/>')
            .addClass('qq-filename')
            .text(filename)
            .append($('<a/>')
                .attr('href', 'javascript:void(null)')
                .addClass('remove')
                .text('verwijder')
                .click(function() {
                    elm.trigger('resetUpload');
                    elm.find('.qq-upload-list').empty();
                    elm.show();
                    $(this).parent().remove();
                })
            )
        );
        elm.hide();
    }; 
    		
	return {
		
		init: function() {

            $('form.ACCrudEdit').data('onSave', onSave);
            $('form.ACCrudEdit').data('onError', onError);
            $('form.ACCrudEdit').data('onBeforeSubmit', onBeforeSubmit);
            
            $('form.ACCrudEdit input[name=_upload]').change(onImageUpload);
            $('form.ACCrudEdit input[name=_embed]').on('change keyup', onEmbedChange);
            $('form.ACCrudEdit input[name=_upload]').next('.ACAjaxUpload').bind('resetUpload', onImageReset);
           
            var filename;
            if (filename = $('form.ACCrudEdit input[name=_files]').val()) {
                if ($('select[name=type]').val() == 'image') {
                    emulateUploadComplete('image', filename);
                } else {
                    emulateUploadComplete('files', filename);
                }
            }
            
            $typefield = $(':input[name=' + $('.media').data('typefield') + ']');
            $thumbfield = $(':input[name=' + $('.media').data('thumbfield') + ']');

            // init embed on edit
            if ($('form.ACCrudEdit input[name=embed]').val()) {
                $('form.ACCrudEdit input[name=embed]').change();
            }
		}
		
	};
}();

$(document).ready(function() {
	AC.Crud.Edit.Media.init();
});