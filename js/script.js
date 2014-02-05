jQuery(document).ready(function($){
	/* WP Image Uploader */
  var _custom_media = true,
      _orig_send_attachment = wp.media.editor.send.attachment;

  $('.uploader input.upload').click(function(e) {
    var send_attachment_bkp = wp.media.editor.send.attachment;
    var button = $(this);
    var id = button.attr('id').replace('_button', '');
    _custom_media = true;
    wp.media.editor.send.attachment = function(props, attachment){
      if ( _custom_media ) {
        $("#"+id).val(attachment.url);
				$("#"+id+"_preview").attr("src", attachment.url).removeAttr("width").removeAttr("height");
				$("#"+id+"_attachment_id").val(attachment.id);
      } else {
        return _orig_send_attachment.apply( this, [props, attachment] );
      };
    }

    wp.media.editor.open(button);
    return false;
  });

  $('.add_media').on('click', function(){
    _custom_media = false;
  });

	/* WP Color Selector */
	function updateColorPickers(){
    $('.patched-up-form .color-text').each(function(){
      $(this).wpColorPicker({
        	// you can declare a default color here,
        	// or in the data-default-color attribute on the input
        	defaultColor: false,
        	// a callback to fire whenever the color changes to a valid color
        	change: function(event, ui){},
        	// a callback to fire when the input is emptied or an invalid color
        	clear: function() {},
        	// hide the color picker controls on load
        	hide: true
        	// show a group of common colors beneath the square
        	// or, supply an array of colors to customize further
        	//palettes: ['#ffffff','#000000','#ff7c0b']
    		});
			});
    }
    updateColorPickers();
    $(document).ajaxSuccess(function(e, xhr, settings) {

        if(settings.data.search('action=save-widget') != -1 ) {
            $('.color-field .wp-picker-container').remove();
            updateColorPickers();
        }
    });
});
