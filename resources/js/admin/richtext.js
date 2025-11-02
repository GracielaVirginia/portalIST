// Inicializador genérico para todos los <div data-richtext>
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

function buildToolbar(el) {
    // Puedes personalizar el toolbar aquí o por data-attr
    // data-toolbar="basic" | "full"
    const mode = el.dataset.toolbar || 'basic';
    if (mode === 'full') {
        return [
            [{ header: [1, 2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ list: 'ordered' }, { list: 'bullet' }],
            [{ script: 'sub' }, { script: 'super' }],
            [{ color: [] }, { background: [] }],
            [{ align: [] }],
            ['link', 'clean']
        ];
    }
    // basic
    return [
        ['bold', 'italic', 'underline'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        ['link', 'clean']
    ];
}

export function initRichtext(root = document) {
    const nodes = root.querySelectorAll('[data-richtext]');
    nodes.forEach((wrap) => {
        // Evita doble init
        if (wrap.__quillInited) return;
        wrap.__quillInited = true;

        const inputSelector = wrap.dataset.input || 'textarea';
        const input = wrap.querySelector(inputSelector);
        const editor = wrap.querySelector('[data-editor]');
        const height = wrap.dataset.height || '260px';

        if (!input || !editor) return;

        editor.style.minHeight = height;

        const quill = new Quill(editor, {
            theme: 'snow',
            modules: { toolbar: buildToolbar(wrap) },
            placeholder: wrap.dataset.placeholder || 'Escribe el contenido…'
        });

        // Carga valor inicial (HTML)
        const initialHtml = input.value?.trim() || '';
        if (initialHtml) quill.clipboard.dangerouslyPasteHTML(initialHtml);

        // Sincroniza en cambios y al enviar el form
        const sync = () => { input.value = editor.querySelector('.ql-editor').innerHTML; };
        quill.on('text-change', sync);

        const form = wrap.closest('form');
        if (form) form.addEventListener('submit', sync);
    });
}

// Auto-init al cargar
document.addEventListener('DOMContentLoaded', () => initRichtext());
