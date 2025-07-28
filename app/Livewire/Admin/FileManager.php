<?php

namespace App\Livewire\Admin;

use App\Models\FileAttachment;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

class FileManager extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $selectedType = '';
    public $selectedUser = '';
    public $perPage = 15;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'selectedType' => ['except' => ''],
        'selectedUser' => ['except' => ''],
    ];

    public function mount()
    {
        $this->authorize('manage_attachments');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedType()
    {
        $this->resetPage();
    }

    public function updatingSelectedUser()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function deleteFile($fileId)
    {
        $this->authorize('delete_any_attachment');
        
        $file = FileAttachment::findOrFail($fileId);
        
        // Delete physical file
        Storage::delete('public/' . $file->file_path);
        
        // Delete thumbnail if exists
        $thumbnailPath = str_replace('.', '_thumb.', $file->file_path);
        if (Storage::exists('public/' . $thumbnailPath)) {
            Storage::delete('public/' . $thumbnailPath);
        }
        
        // Delete database record
        $file->delete();
        
        session()->flash('success', 'File deleted successfully.');
    }

    public function getFileTypeOptions()
    {
        return [
            '' => 'All Types',
            'image' => 'Images',
            'video' => 'Videos',
            'audio' => 'Audio',
            'document' => 'Documents',
            'other' => 'Other',
        ];
    }

    public function getUsers()
    {
        return User::select('id', 'name')
            ->whereHas('fileAttachments')
            ->orderBy('name')
            ->get();
    }

    public function getTotalStats()
    {
        $stats = [
            'total_files' => FileAttachment::count(),
            'total_size' => FileAttachment::sum('file_size'),
            'total_downloads' => FileAttachment::sum('download_count'),
            'total_images' => FileAttachment::where('is_image', true)->count(),
        ];

        return $stats;
    }

    public function render()
    {
        $query = FileAttachment::with(['user', 'attachable'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('original_name', 'like', '%' . $this->search . '%')
                      ->orWhere('file_name', 'like', '%' . $this->search . '%')
                      ->orWhereHas('user', function ($userQuery) {
                          $userQuery->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->selectedType, function ($query) {
                switch ($this->selectedType) {
                    case 'image':
                        $query->where('is_image', true);
                        break;
                    case 'video':
                        $query->where('mime_type', 'like', 'video/%');
                        break;
                    case 'audio':
                        $query->where('mime_type', 'like', 'audio/%');
                        break;
                    case 'document':
                        $query->where('mime_type', 'like', 'application/%')
                              ->orWhere('mime_type', 'like', 'text/%');
                        break;
                    case 'other':
                        $query->where('is_image', false)
                              ->where('mime_type', 'not like', 'video/%')
                              ->where('mime_type', 'not like', 'audio/%')
                              ->where('mime_type', 'not like', 'application/%')
                              ->where('mime_type', 'not like', 'text/%');
                        break;
                }
            })
            ->when($this->selectedUser, function ($query) {
                $query->where('user_id', $this->selectedUser);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        $files = $query->paginate($this->perPage);
        $stats = $this->getTotalStats();
        $users = $this->getUsers();

        return view('livewire.admin.file-manager', [
            'files' => $files,
            'stats' => $stats,
            'users' => $users,
            'fileTypeOptions' => $this->getFileTypeOptions(),
        ])->layout('layouts.admin');
    }
}
