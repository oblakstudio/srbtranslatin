tinymce.PluginManager.add('stl_cyr', function( editor, url ) {
   editor.addButton('stl_cyr', {
        icon: false,
        image: stl.button_icon,
        onclick: function() {
            
            selection = editor.selection.getContent();

            editor.selection.setContent('[stl_cyr]' + selection + '[/stl_cyr]');

        }
     });
});

