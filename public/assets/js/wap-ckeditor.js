

if (CKEDITOR.env.ie && CKEDITOR.env.version < 9)
    CKEDITOR.tools.enableHtml5Elements(document);


CKEDITOR.config.height = 560;
CKEDITOR.config.width = 'auto';

var initSample = (function () {
    var wysiwygareaAvailable = isWysiwygareaAvailable(),
     isBBCodeBuiltIn = !!CKEDITOR.plugins.get('bbcode');

    return function () {
        var editorElement = CKEDITOR.document.getById('editor');

        // :(((
        if (isBBCodeBuiltIn) {
            editorElement.setHtml(
            'Hello world!\n\n' +
            'I\'m an instance of [url=http://ckeditor.com]CKEditor[/url].'
            );
        }
        // Depending on the wysiwygare plugin availability initialize classic or inline editor.
        if (wysiwygareaAvailable) {
            CKEDITOR.replace('editor',{
                toolbarGroups: [
                        { "name": "clipboard", "groups": [ 'Undo', 'Redo' ] },
                        {"name":"basicstyles","groups":["basicstyles"]},
                        {"name":"links","groups":["links","Unlink"]},
                        {"name":"paragraph","groups":["list","blocks",'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote']},
                        {"name":"document","groups":["mode"]},
                        {"name":"insert","groups":["Image", "EmbedSemantic", "Table"]},
                        {"name":"styles","groups":["styles","Format"]},
                        { "name": "editing", "groups": [ 'Scayt' ] },
                        {"name":"about","groups":["about"]}
                ]
            });
        } else {
            editorElement.setAttribute('contenteditable', 'true');
            CKEDITOR.inline('editor');

            // TODO we can consider displaying some info box that
            // without wysiwygarea the classic editor may not work.
        }
    };

    function isWysiwygareaAvailable() {
        // If in development mode, then the wysiwygarea must be available.
        // Split REV into two strings so builder does not replace it :D.
        if (CKEDITOR.revision == ('%RE' + 'V%')) {
            return true;
        }

        return !!CKEDITOR.plugins.get('wysiwygarea');
    }
})();

