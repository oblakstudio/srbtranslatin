tinymce.PluginManager.add('stl_cyr', function (editor, url) {
    editor.addButton('stl_cyr', {
        title: stl.title,
        image: stl.button_icon,
        onclick: function () {
            // Open window
            editor.windowManager.open({
                title: stl.title,
                body: [{
                    type: 'listbox',
                    name: 'shortcode',
                    label: stl.label,
                    values: [
                        {text: stl.cyr, value: 'stl_cyr'},
                        {text: stl.show, value: 'stl_show'},
                        {text: stl.translit, value: 'stl_translit'},
                    ]
                }],
                onsubmit: function (e) {
                    // Insert content when the window form is submitted

                    selection = editor.selection.getContent();

                    var sc_string = '';

                    switch( e.data.shortcode) {

                        case 'stl_cyr' :
                            sc_string += '[stl_cyr]' + selection + '[/stl_cyr]';
                            break;

                        case 'stl_show' :
                            sc_string += '[stl_show script="cir"]' + selection + '[/stl_show]';
                            break;

                        case 'stl_translit' :
                            sc_string += '[stl_translit latin=""]' + selection + '[/stl_translit]';
                            break;

                        default:
                            sc_string += '';
                    }

                    editor.insertContent(sc_string);
                },
                width: 400,
                height: 200
            });
        }
    });
});