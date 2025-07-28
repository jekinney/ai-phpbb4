<div>
    <div class="rich-text-editor-wrapper">
        <textarea 
            wire:model="content"
            id="{{ $editorId }}"
            class="tinymce-editor w-full border border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="{{ $placeholder }}"
            wire-id="{{ $this->getId() }}"
        >{{ $content }}</textarea>
    </div>

    @script
    <script>
        // Initialize TinyMCE when component loads
        $wire.on('initEditor', function() {
            initTinyMCEEditor();
        });
        
        function initTinyMCEEditor() {
            // Clean up existing editor first
            if (tinymce.get('{{ $editorId }}')) {
                tinymce.remove('#{{ $editorId }}');
            }
            
            // Wait a bit then initialize
            setTimeout(() => {
                if (typeof window.initTinyMCE === 'function') {
                    window.initTinyMCE('#{{ $editorId }}', {
                        height: {{ $height }},
                        placeholder: '{{ $placeholder }}'
                    });
                }
            }, 100);
        }
        
        // Initialize on mount
        document.addEventListener('DOMContentLoaded', function() {
            initTinyMCEEditor();
        });
        
        // Reinitialize when Livewire updates this component
        Livewire.hook('morph.updated', ({ el, component }) => {
            if (component.id === $wire.__instance.id) {
                initTinyMCEEditor();
            }
        });
    </script>
    @endscript
</div>
