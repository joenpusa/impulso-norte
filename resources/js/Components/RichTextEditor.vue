<script setup>
import { useEditor, EditorContent, NodeViewWrapper } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import { Table, TableRow, TableCell, TableHeader } from '@tiptap/extension-table'
import Image from '@tiptap/extension-image'
import Link from '@tiptap/extension-link'
import Underline from '@tiptap/extension-underline'
import TextAlign from '@tiptap/extension-text-align'
import Placeholder from '@tiptap/extension-placeholder'
import { TextStyle } from '@tiptap/extension-text-style'
import { Color } from '@tiptap/extension-color'
import { Youtube } from '@tiptap/extension-youtube'
import { watch, onBeforeUnmount, ref } from 'vue'

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: 'Escriba aquí...',
    },
})

const emit = defineEmits(['update:modelValue'])

// Custom TableCell extension to support vertical alignment
const CustomTableCell = TableCell.extend({
    addAttributes() {
        return {
            ...this.parent?.(),
            verticalAlign: {
                default: null,
                parseHTML: (element) => element.style.verticalAlign,
                renderHTML: (attributes) => {
                    if (!attributes.verticalAlign) {
                        return {}
                    }
                    return {
                        style: `vertical-align: ${attributes.verticalAlign}`,
                    }
                },
            },
        }
    },
})

const CustomTableHeader = TableHeader.extend({
     addAttributes() {
        return {
            ...this.parent?.(),
            verticalAlign: {
                default: null,
                parseHTML: (element) => element.style.verticalAlign,
                renderHTML: (attributes) => {
                    if (!attributes.verticalAlign) {
                        return {}
                    }
                    return {
                        style: `vertical-align: ${attributes.verticalAlign}`,
                    }
                },
            },
        }
    },
})

// Custom Image extension to allow resizing via width attribute and style
const CustomImage = Image.extend({
    addAttributes() {
        return {
            ...this.parent?.(),
            width: {
                default: null,
            },
            height: {
                default: null,
            },
            style: {
                default: null,
                parseHTML: (element) => element.getAttribute('style'),
                renderHTML: (attributes) => {
                     let style = attributes.style || '';
                     // Ensure width is also in style for consistent preview/render if width attr exists
                     if (attributes.width && !style.includes('width')) {
                         style += `; width: ${attributes.width}`; 
                     }
                     if (!style) return {};
                     return { style };
                },
            },
        }
    }
})

const editor = useEditor({
    extensions: [
        StarterKit,
        Table.configure({
            resizable: true,
            handleWidth: 5,
        }),
        TableRow,
        CustomTableCell,
        CustomTableHeader,
        CustomImage.configure({
             allowBase64: true,
        }),
        Underline,
        Link.configure({
            openOnClick: false,
        }),
        TextAlign.configure({
            types: ['heading', 'paragraph', 'tableCell', 'tableHeader'],
        }),
        Placeholder.configure({
            placeholder: props.placeholder,
        }),
        TextStyle,
        Color,
        Youtube.configure({
            controls: false,
        }),
    ],
    content: props.modelValue,
    onUpdate: ({ editor }) => {
        emit('update:modelValue', editor.getHTML())
    },
    editorProps: {
        attributes: {
            class: 'prose prose-sm sm:prose-base lg:prose-lg xl:prose-2xl m-5 focus:outline-none min-h-[150px]',
        },
    },
})

watch(() => props.modelValue, (newValue) => {
    const isSame = editor.value?.getHTML() === newValue
    if (!isSame && editor.value) {
        editor.value.commands.setContent(newValue, false)
    }
})

onBeforeUnmount(() => {
    editor.value?.destroy()
})

const addParamsToUrl = (url) => {
    try {
        const newUrl = new URL(url)
        return newUrl.href
    } catch (e) {
        return url
    }
}

const setLink = () => {
    const previousUrl = editor.value.getAttributes('link').href
    const url = window.prompt('URL', previousUrl)
    if (url === null) return
    if (url === '') {
        editor.value.chain().focus().extendMarkRange('link').unsetLink().run()
        return
    }
    editor.value.chain().focus().extendMarkRange('link').setLink({ href: addParamsToUrl(url) }).run()
}

const addImage = () => {
    const url = window.prompt('URL imagen')
    if (url) {
        editor.value.chain().focus().setImage({ src: url }).run()
    }
}

// Resize now sets both width attribute and style
const resizeImage = () => {
    if (!editor.value.isActive('image')) return;
    
    const currentWidth = editor.value.getAttributes('image').width || 'auto';
    const width = window.prompt('Ancho de la imagen (ej: 300px, 50%, auto)', currentWidth);
    
    if (width !== null) {
         editor.value.chain().focus().updateAttributes('image', { 
             width: width,
             style: `width: ${width}; height: auto;` 
         }).run();
    }
}

const addYoutube = () => {
    const url = window.prompt('URL de YouTube')
    if (url) {
         editor.value.commands.setYoutubeVideo({ src: url })
    }
}

const setCellVerticalAlign = (align) => {
    editor.value.chain().focus().setCellAttribute('verticalAlign', align).run()
}

const setColumnWidth = () => {
    const width = window.prompt('Ancho de columna (px)', '200');
    if (width) {
        // Tiptap table extension uses colwidth attribute on cells which is an array of widths
        // for spans. We just set single value for now as we assume single cell selection or similar.
        // NOTE: Standard way in Tiptap is dragging, but manual set provides precision.
        // We iterate over selected cells or just current cell and set colwidth attribute.
        // Using `setCellAttribute` for `colwidth` expects an array `[width]`.
        // We format it as a number if possible.
        const widthNum = parseInt(width, 10);
        if (!isNaN(widthNum)) {
             editor.value.chain().focus().setCellAttribute('colwidth', [widthNum]).run();
             // Sometimes fixTables is needed to re-render colgroups
             setTimeout(() => {
                 editor.value.chain().focus().fixTables().run();
             }, 10);
        }
    }
}

</script>

<template>
    <div v-if="editor" class="border border-gray-300 rounded-md overflow-hidden bg-white shadow-sm">
        <!-- Toolbar -->
        <div class="border-b border-gray-200 bg-gray-50 p-2 flex flex-wrap gap-1">
            
            <!-- History (Undo/Redo) -->
            <div class="flex gap-1 border-r border-gray-300 pr-2 mr-2">
                <button type="button" @click="editor.chain().focus().undo().run()" :disabled="!editor.can().chain().focus().undo().run()" :class="{ 'opacity-50 cursor-not-allowed': !editor.can().chain().focus().undo().run() }" class="p-1.5 rounded hover:bg-gray-200" title="Deshacer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7v6h6"></path><path d="M21 17a9 9 0 0 0-9-9 9 9 0 0 0-6 2.3L3 13"></path></svg>
                </button>
                <button type="button" @click="editor.chain().focus().redo().run()" :disabled="!editor.can().chain().focus().redo().run()" :class="{ 'opacity-50 cursor-not-allowed': !editor.can().chain().focus().redo().run() }" class="p-1.5 rounded hover:bg-gray-200" title="Rehacer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 7v6h-6"></path><path d="M3 17a9 9 0 0 1 9-9 9 9 0 0 1 6 2.3l2.7 2.7"></path></svg>
                </button>
            </div>

            <!-- Basic Formatting -->
            <div class="flex gap-1 border-r border-gray-300 pr-2 mr-2">
                <button type="button" @click="editor.chain().focus().toggleBold().run()" :class="{ 'bg-gray-200 text-black': editor.isActive('bold') }" class="p-1.5 rounded hover:bg-gray-200" title="Negrita">
                    <b>B</b>
                </button>
                <button type="button" @click="editor.chain().focus().toggleItalic().run()" :class="{ 'bg-gray-200 text-black': editor.isActive('italic') }" class="p-1.5 rounded hover:bg-gray-200" title="Cursiva">
                    <i>I</i>
                </button>
                <button type="button" @click="editor.chain().focus().toggleUnderline().run()" :class="{ 'bg-gray-200 text-black': editor.isActive('underline') }" class="p-1.5 rounded hover:bg-gray-200" title="Subrayado">
                    <u>U</u>
                </button>
                <button type="button" @click="editor.chain().focus().toggleStrike().run()" :class="{ 'bg-gray-200 text-black': editor.isActive('strike') }" class="p-1.5 rounded hover:bg-gray-200" title="Tachado">
                    <s>S</s>
                </button>
                <input type="color" @input="editor.chain().focus().setColor($event.target.value).run()" :value="editor.getAttributes('textStyle').color" class="w-8 h-8 p-0 border-0 rounded cursor-pointer" title="Color de texto">
            </div>

            <!-- Headings -->
            <div class="flex gap-1 border-r border-gray-300 pr-2 mr-2">
                <button type="button" @click="editor.chain().focus().toggleHeading({ level: 1 }).run()" :class="{ 'bg-gray-200 text-black': editor.isActive('heading', { level: 1 }) }" class="p-1.5 rounded hover:bg-gray-200 font-bold text-sm" title="Título 1">
                    H1
                </button>
                <button type="button" @click="editor.chain().focus().toggleHeading({ level: 2 }).run()" :class="{ 'bg-gray-200 text-black': editor.isActive('heading', { level: 2 }) }" class="p-1.5 rounded hover:bg-gray-200 font-bold text-sm" title="Título 2">
                    H2
                </button>
                <button type="button" @click="editor.chain().focus().toggleHeading({ level: 3 }).run()" :class="{ 'bg-gray-200 text-black': editor.isActive('heading', { level: 3 }) }" class="p-1.5 rounded hover:bg-gray-200 font-bold text-sm" title="Título 3">
                    H3
                </button>
            </div>

            <!-- Alignment -->
            <div class="flex gap-1 border-r border-gray-300 pr-2 mr-2">
                 <button type="button" @click="editor.chain().focus().setTextAlign('left').run()" :class="{ 'bg-gray-200 text-black': editor.isActive({ textAlign: 'left' }) }" class="p-1.5 rounded hover:bg-gray-200" title="Izquierda">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="17" y1="10" x2="3" y2="10"></line><line x1="21" y1="6" x2="3" y2="6"></line><line x1="21" y1="14" x2="3" y2="14"></line><line x1="17" y1="18" x2="3" y2="18"></line></svg>
                </button>
                <button type="button" @click="editor.chain().focus().setTextAlign('center').run()" :class="{ 'bg-gray-200 text-black': editor.isActive({ textAlign: 'center' }) }" class="p-1.5 rounded hover:bg-gray-200" title="Centro">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="21" y1="10" x2="3" y2="10"></line><line x1="21" y1="6" x2="3" y2="6"></line><line x1="21" y1="14" x2="3" y2="14"></line><line x1="21" y1="18" x2="3" y2="18"></line></svg>
                </button>
                 <button type="button" @click="editor.chain().focus().setTextAlign('right').run()" :class="{ 'bg-gray-200 text-black': editor.isActive({ textAlign: 'right' }) }" class="p-1.5 rounded hover:bg-gray-200" title="Derecha">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="21" y1="10" x2="7" y2="10"></line><line x1="21" y1="6" x2="3" y2="6"></line><line x1="21" y1="14" x2="3" y2="14"></line><line x1="21" y1="18" x2="7" y2="18"></line></svg>
                </button>
                 <button type="button" @click="editor.chain().focus().setTextAlign('justify').run()" :class="{ 'bg-gray-200 text-black': editor.isActive({ textAlign: 'justify' }) }" class="p-1.5 rounded hover:bg-gray-200" title="Justificado">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="21" y1="10" x2="3" y2="10"></line><line x1="21" y1="6" x2="3" y2="6"></line><line x1="21" y1="14" x2="3" y2="14"></line><line x1="21" y1="18" x2="3" y2="18"></line></svg>
                </button>
            </div>

            <!-- Lists -->
            <div class="flex gap-1 border-r border-gray-300 pr-2 mr-2">
                <button type="button" @click="editor.chain().focus().toggleBulletList().run()" :class="{ 'bg-gray-200 text-black': editor.isActive('bulletList') }" class="p-1.5 rounded hover:bg-gray-200" title="Lista con viñetas">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                </button>
                 <button type="button" @click="editor.chain().focus().toggleOrderedList().run()" :class="{ 'bg-gray-200 text-black': editor.isActive('orderedList') }" class="p-1.5 rounded hover:bg-gray-200" title="Lista numerada">
                   <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="10" y1="6" x2="21" y2="6"></line><line x1="10" y1="12" x2="21" y2="12"></line><line x1="10" y1="18" x2="21" y2="18"></line><path d="M4 6h1v4"></path><path d="M4 10h2"></path><path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1"></path></svg>
                </button>
            </div>

            <!-- Tables -->
            <div class="flex gap-1 border-r border-gray-300 pr-2 mr-2">
                <button type="button" @click="editor.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run()" class="p-1.5 rounded hover:bg-gray-200" title="Insertar Tabla">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="3" y1="15" x2="21" y2="15"></line><line x1="9" y1="3" x2="9" y2="21"></line><line x1="15" y1="3" x2="15" y2="21"></line></svg>
                </button>
                <div v-if="editor.isActive('table')" class="flex gap-1 items-center">
                    <button type="button" @click="editor.chain().focus().addColumnBefore().run()" class="p-1.5 rounded hover:bg-gray-200 text-xs" title="Agregar columna antes">+Col Izq</button>
                    <button type="button" @click="editor.chain().focus().addColumnAfter().run()" class="p-1.5 rounded hover:bg-gray-200 text-xs" title="Agregar columna después">+Col Der</button>
                    <button type="button" @click="editor.chain().focus().deleteColumn().run()" class="p-1.5 rounded hover:bg-gray-200 text-xs text-red-600" title="Eliminar columna">xCol</button>
                    <button type="button" @click="editor.chain().focus().addRowBefore().run()" class="p-1.5 rounded hover:bg-gray-200 text-xs" title="Agregar fila antes">+Fila Arr</button>
                    <button type="button" @click="editor.chain().focus().addRowAfter().run()" class="p-1.5 rounded hover:bg-gray-200 text-xs" title="Agregar fila después">+Fila Abj</button>
                    <button type="button" @click="editor.chain().focus().deleteRow().run()" class="p-1.5 rounded hover:bg-gray-200 text-xs text-red-600" title="Eliminar fila">xFila</button>
                    <button type="button" @click="editor.chain().focus().mergeCells().run()" class="p-1.5 rounded hover:bg-gray-200 text-xs" title="Combinar celdas">Mer</button>
                    <button type="button" @click="editor.chain().focus().splitCell().run()" class="p-1.5 rounded hover:bg-gray-200 text-xs" title="Separar celdas">Spl</button>
                     <!-- Vertical Align -->
                    <div class="flex gap-0.5 ml-1 border-l pl-1">
                        <button type="button" @click="setCellVerticalAlign('top')" class="p-1 rounded hover:bg-gray-200 text-xs" title="Alinear Arriba">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 3h14"/><path d="M12 8v13"/><path d="M8 12l4-4 4 4"/></svg>
                        </button>
                         <button type="button" @click="setCellVerticalAlign('middle')" class="p-1 rounded hover:bg-gray-200 text-xs" title="Alinear Centro Vertical">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M12 5v14"/><path d="M8 9l4-4 4 4"/><path d="M8 15l4 4 4-4"/></svg>
                        </button>
                        <button type="button" @click="setCellVerticalAlign('bottom')" class="p-1 rounded hover:bg-gray-200 text-xs" title="Alinear Abajo">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 21h14"/><path d="M12 3v13"/><path d="M8 12l4 4 4-4"/></svg>
                        </button>
                    </div>
                     <!-- Col Width -->
                     <button type="button" @click="setColumnWidth" class="p-1 rounded hover:bg-gray-200 text-xs ml-1 border-l pl-1" title="Ancho de Columna">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16"/><path d="M4 18h16"/><path d="M10 2v20"/><path d="M14 2v20"/></svg>
                    </button>
                </div>
            </div>

            <!-- Media -->
             <div class="flex gap-1">
                <button type="button" @click="setLink" :class="{ 'bg-gray-200 text-black': editor.isActive('link') }" class="p-1.5 rounded hover:bg-gray-200" title="Enlace">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                </button>
                <div class="relative group">
                     <button type="button" @click="addImage" :class="{ 'bg-gray-200 text-black': editor.isActive('image') }" class="p-1.5 rounded hover:bg-gray-200" title="Imagen">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                    </button>
                    <!-- Resize controls visible when image is selected -->
                     <div v-if="editor.isActive('image')" class="absolute top-full left-0 mt-1 bg-white border shadow rounded z-10 hidden group-hover:block p-1">
                        <button type="button" @click="resizeImage" class="text-xs whitespace-nowrap p-1 hover:bg-gray-100 w-full text-left">Redimensionar</button>
                    </div>
                </div>
                 <button type="button" @click="addYoutube" :class="{ 'bg-gray-200 text-black': editor.isActive('youtube') }" class="p-1.5 rounded hover:bg-gray-200" title="YouTube video">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon></svg>
                </button>
            </div>
        </div>

        <!-- Content -->
        <editor-content :editor="editor" class="p-4 min-h-[300px]" />
    </div>
</template>

<style lang="css">
/* Basic Editor Styles */
.ProseMirror {
    outline: none;
    min-height: 150px;
}

.ProseMirror p.is-editor-empty:first-child::before {
  content: attr(data-placeholder);
  float: left;
  color: #adb5bd;
  pointer-events: none;
  height: 0;
}

/* Table Styles */
.ProseMirror table {
  border-collapse: collapse;
  table-layout: fixed;
  width: 100%;
  margin: 0;
  overflow: hidden;
}

.ProseMirror td,
.ProseMirror th {
  min-width: 1em;
  border: 2px solid #ced4da;
  padding: 3px 5px;
  vertical-align: top;
  box-sizing: border-box;
  position: relative;
}

.ProseMirror th {
  font-weight: bold;
  text-align: left;
  background-color: #f1f3f5;
}

.ProseMirror .selectedCell:after {
  z-index: 2;
  position: absolute;
  content: "";
  left: 0; right: 0; top: 0; bottom: 0;
  background: rgba(200, 200, 255, 0.4);
  pointer-events: none;
}

.ProseMirror .column-resize-handle {
  position: absolute;
  right: -2px;
  top: 0;
  bottom: -2px;
  width: 4px;
  background-color: #adf;
  pointer-events: none;
}

/* Image Styles */
.ProseMirror img {
  display: block;
  max-width: 100%;
  height: auto;
}

.ProseMirror img.ProseMirror-selectednode {
  outline: 3px solid #68CEF8;
}

/* Youtube */
.ProseMirror div[data-youtube-video] {
  cursor: move;
  padding-right: 24px;
}

.ProseMirror iframe {
    width: 100%;
    height: auto;
    aspect-ratio: 16/9;
}
</style>
