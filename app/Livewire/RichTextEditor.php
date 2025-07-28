<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;

class RichTextEditor extends Component
{
    use WithFileUploads;

    public $content = '';
    public $editorId;
    public $placeholder = 'Start typing...';
    public $height = 300;
    public $enableFileUpload = true;
    public $maxFileSize = 10240; // 10MB in KB
    public $allowedTypes = 'image/*,video/*,audio/*,.pdf,.doc,.docx,.txt';

    protected $listeners = [
        'updateContent' => 'updateContent',
    ];

    public function mount($content = '', $editorId = null, $placeholder = null, $height = null)
    {
        $this->content = $content;
        $this->editorId = $editorId ?: 'editor_' . uniqid();
        $this->placeholder = $placeholder ?: $this->placeholder;
        $this->height = $height ?: $this->height;
    }

    public function updateContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function render()
    {
        return view('livewire.rich-text-editor');
    }
}
