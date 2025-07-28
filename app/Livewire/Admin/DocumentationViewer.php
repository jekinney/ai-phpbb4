<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DocumentationViewer extends Component
{
    public $selectedFile = '';
    public $fileContent = '';
    public $availableFiles = [];
    public $searchTerm = '';

    protected $listeners = ['refreshFiles' => 'loadAvailableFiles'];

    public function mount()
    {
        $this->authorize('view_documentation');
        $this->loadAvailableFiles();
        
        // Auto-select first file if available
        if (!empty($this->availableFiles)) {
            $this->selectedFile = $this->availableFiles[0]['path'];
            $this->loadFileContent();
        }
    }

    public function loadAvailableFiles()
    {
        $docsPath = base_path('docs');
        $this->availableFiles = [];

        if (File::exists($docsPath)) {
            $files = File::allFiles($docsPath);
            
            foreach ($files as $file) {
                if (in_array($file->getExtension(), ['md', 'txt', 'rst'])) {
                    $this->availableFiles[] = [
                        'name' => $file->getFilename(),
                        'path' => $file->getPathname(),
                        'relative_path' => str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file->getPathname()),
                        'size' => $file->getSize(),
                        'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                        'extension' => $file->getExtension(),
                    ];
                }
            }
        }

        // Also include root-level documentation files
        $rootFiles = [
            'README.md',
            'CHANGELOG.md',
            'CONTRIBUTING.md',
            'LICENSE.md',
            'QUOTE_FUNCTIONALITY.md',
            'RICH_TEXT_EDITOR.md',
        ];

        foreach ($rootFiles as $filename) {
            $filepath = base_path($filename);
            if (File::exists($filepath)) {
                $file = new \SplFileInfo($filepath);
                $this->availableFiles[] = [
                    'name' => $filename,
                    'path' => $filepath,
                    'relative_path' => $filename,
                    'size' => $file->getSize(),
                    'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                    'extension' => $file->getExtension(),
                ];
            }
        }

        // Sort files by name
        usort($this->availableFiles, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });
    }

    public function selectFile($filePath)
    {
        $this->selectedFile = $filePath;
        $this->loadFileContent();
    }

    public function loadFileContent()
    {
        if (empty($this->selectedFile) || !File::exists($this->selectedFile)) {
            $this->fileContent = 'File not found.';
            return;
        }

        try {
            $this->fileContent = File::get($this->selectedFile);
        } catch (\Exception $e) {
            $this->fileContent = 'Error reading file: ' . $e->getMessage();
        }
    }

    public function getFilteredFiles()
    {
        if (empty($this->searchTerm)) {
            return $this->availableFiles;
        }

        return array_filter($this->availableFiles, function($file) {
            return Str::contains(strtolower($file['name']), strtolower($this->searchTerm)) ||
                   Str::contains(strtolower($file['relative_path']), strtolower($this->searchTerm));
        });
    }

    public function downloadFile($filePath)
    {
        if (!File::exists($filePath)) {
            session()->flash('error', 'File not found.');
            return;
        }

        return response()->download($filePath);
    }

    public function getFileSizeHuman($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function render()
    {
        return view('livewire.admin.documentation-viewer', [
            'filteredFiles' => $this->getFilteredFiles(),
        ])->layout('layouts.admin');
    }
}
