/**
 * TinyMCE version 8.0.1 (2025-07-28)
 */

(function () {
    'use strict';

    var global$1 = tinymce.util.Tools.resolve('tinymce.PluginManager');

    /* eslint-disable @typescript-eslint/no-wrapper-object-types */
    const isSimpleType = (type) => (value) => typeof value === type;
    const isBoolean = isSimpleType('boolean');
    const isNumber = isSimpleType('number');

    const option = (name) => (editor) => editor.options.get(name);
    const register$2 = (editor) => {
        const registerOption = editor.options.register;
        registerOption('nonbreaking_force_tab', {
            processor: (value) => {
                if (isBoolean(value)) {
                    return { value: value ? 3 : 0, valid: true };
                }
                else if (isNumber(value)) {
                    return { value, valid: true };
                }
                else {
                    return { valid: false, message: 'Must be a boolean or number.' };
                }
            },
            default: false
        });
        registerOption('nonbreaking_wrap', {
            processor: 'boolean',
            default: true
        });
    };
    const getKeyboardSpaces = option('nonbreaking_force_tab');
    const wrapNbsps = option('nonbreaking_wrap');

    const stringRepeat = (string, repeats) => {
        let str = '';
        for (let index = 0; index < repeats; index++) {
            str += string;
        }
        return str;
    };
    const isVisualCharsEnabled = (editor) => editor.plugins.visualchars ? editor.plugins.visualchars.isEnabled() : false;
    const insertNbsp = (editor, times) => {
        const classes = () => isVisualCharsEnabled(editor) ? 'mce-nbsp-wrap mce-nbsp' : 'mce-nbsp-wrap';
        const nbspSpan = () => `<span class="${classes()}" contenteditable="false">${stringRepeat('&nbsp;', times)}</span>`;
        const shouldWrap = wrapNbsps(editor);
        const html = shouldWrap || editor.plugins.visualchars ? nbspSpan() : stringRepeat('&nbsp;', times);
        editor.undoManager.transact(() => editor.insertContent(html));
    };

    const register$1 = (editor) => {
        editor.addCommand('mceNonBreaking', () => {
            insertNbsp(editor, 1);
        });
    };

    var global = tinymce.util.Tools.resolve('tinymce.util.VK');

    const setup = (editor) => {
        const spaces = getKeyboardSpaces(editor);
        if (spaces > 0) {
            editor.on('keydown', (e) => {
                if (e.keyCode === global.TAB && !e.isDefaultPrevented()) {
                    if (e.shiftKey) {
                        return;
                    }
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    insertNbsp(editor, spaces);
                }
            });
        }
    };

    const onSetupEditable = (editor) => (api) => {
        const nodeChanged = () => {
            api.setEnabled(editor.selection.isEditable());
        };
        editor.on('NodeChange', nodeChanged);
        nodeChanged();
        return () => {
            editor.off('NodeChange', nodeChanged);
        };
    };
    const register = (editor) => {
        const onAction = () => editor.execCommand('mceNonBreaking');
        editor.ui.registry.addButton('nonbreaking', {
            icon: 'non-breaking',
            tooltip: 'Nonbreaking space',
            onAction,
            onSetup: onSetupEditable(editor)
        });
        editor.ui.registry.addMenuItem('nonbreaking', {
            icon: 'non-breaking',
            text: 'Nonbreaking space',
            onAction,
            onSetup: onSetupEditable(editor)
        });
    };

    /**
     * This class contains all core logic for the nonbreaking plugin.
     *
     * @class tinymce.nonbreaking.Plugin
     * @private
     */
    var Plugin = () => {
        global$1.add('nonbreaking', (editor) => {
            register$2(editor);
            register$1(editor);
            register(editor);
            setup(editor);
        });
    };

    Plugin();
    /** *****
     * DO NOT EXPORT ANYTHING
     *
     * IF YOU DO ROLLUP WILL LEAVE A GLOBAL ON THE PAGE
     *******/

})();
