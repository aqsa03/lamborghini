import EditorJS from '@editorjs/editorjs';
import Underline from '@editorjs/underline';

const createEditor = ({
    holderId,
    placeholder,
    data,
    readOnly
}) => {
    return new EditorJS({
        holder: holderId,
        placeholder: placeholder ?? 'Scrivi il tuo articolo!',
        data: data ?? {},
        tools: {
            underline: Underline
        },
        readOnly: readOnly === true ? true : false
    });
};

export {
    createEditor
}
