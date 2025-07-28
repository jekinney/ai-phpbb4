import './bootstrap';
import tinymce from 'tinymce/tinymce';

// Import TinyMCE themes and plugins
import 'tinymce/themes/silver';
import 'tinymce/icons/default';
import 'tinymce/plugins/image';
import 'tinymce/plugins/media';
import 'tinymce/plugins/link';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/code';
import 'tinymce/plugins/table';
import 'tinymce/plugins/emoticons';
import 'tinymce/plugins/autoresize';
import 'tinymce/plugins/wordcount';

// TinyMCE Editor Component
window.initTinyMCE = function(selector, options = {}) {
    const element = document.querySelector(selector);
    if (!element) return;
    
    const defaultOptions = {
        target: element,
        height: 300,
        menubar: false,
        plugins: [
            'image', 'media', 'link', 'lists', 'code', 'table', 
            'emoticons', 'autoresize', 'wordcount'
        ],
        toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image media | table | emoticons | code',
        images_upload_handler: function (blobInfo, success, failure) {
            uploadFile(blobInfo.blob(), blobInfo.filename(), success, failure);
        },
        automatic_uploads: true,
        file_picker_types: 'image media',
        file_picker_callback: function(callback, value, meta) {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            
            if (meta.filetype === 'image') {
                input.setAttribute('accept', 'image/*');
            } else if (meta.filetype === 'media') {
                input.setAttribute('accept', 'video/*,audio/*');
            }
            
            input.onchange = function() {
                const file = this.files[0];
                uploadFile(file, file.name, function(url) {
                    callback(url);
                }, function() {
                    console.error('File upload failed');
                });
            };
            
            input.click();
        },
        skin: window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'oxide-dark' : 'oxide',
        content_css: window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'default',
        setup: function (editor) {
            // Sync with Livewire when content changes
            editor.on('change keyup paste', function () {
                const content = editor.getContent();
                const wireId = element.getAttribute('wire-id');
                if (wireId && window.Livewire) {
                    window.Livewire.find(wireId).set('content', content);
                }
            });
        },
        ...options
    };
    
    tinymce.init(defaultOptions);
};

// File upload function
function uploadFile(file, filename, success, failure) {
    const formData = new FormData();
    formData.append('file', file);
    
    fetch('/files/upload', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            success(data.file.url);
        } else {
            failure('Upload failed');
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        failure('Upload failed');
    });
}

// Livewire hooks for TinyMCE
document.addEventListener('livewire:init', () => {
    Livewire.hook('morph.updated', ({ el, component }) => {
        // Reinitialize TinyMCE after Livewire updates
        el.querySelectorAll('.tinymce-editor').forEach(editor => {
            if (editor.id && !tinymce.get(editor.id)) {
                editor.setAttribute('wire-id', component.id);
                initTinyMCE('#' + editor.id);
            }
        });
    });
    
    Livewire.hook('element.removed', ({ el }) => {
        // Clean up TinyMCE when elements are removed
        el.querySelectorAll('.tinymce-editor').forEach(editor => {
            if (editor.id && tinymce.get(editor.id)) {
                tinymce.remove('#' + editor.id);
            }
        });
    });
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.tinymce-editor').forEach(editor => {
        if (editor.id) {
            initTinyMCE('#' + editor.id);
        }
    });
});