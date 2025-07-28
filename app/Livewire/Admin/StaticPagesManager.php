<?php

namespace App\Livewire\Admin;

use App\Models\StaticPage;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StaticPagesManager extends Component
{
    use WithPagination, AuthorizesRequests;

    public $showModal = false;
    public $editingPage = null;
    public $title = '';
    public $slug = '';
    public $meta_description = '';
    public $content = '';
    public $is_active = true;
    public $show_in_footer = true;
    public $sort_order = 0;
    public $search = '';

    protected $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255|regex:/^[a-z0-9-]+$/|unique:static_pages,slug',
        'meta_description' => 'nullable|string|max:500',
        'content' => 'required|string',
        'is_active' => 'boolean',
        'show_in_footer' => 'boolean',
        'sort_order' => 'integer|min:0',
    ];

    protected $messages = [
        'slug.regex' => 'The slug must only contain lowercase letters, numbers, and hyphens.',
        'slug.unique' => 'This slug is already taken.',
    ];

    public function mount()
    {
        $this->authorize('manage_static_pages');
    }

    public function updatedTitle()
    {
        if (!$this->editingPage) {
            $this->slug = \Illuminate\Support\Str::slug($this->title);
        }
    }

    public function render()
    {
        $pages = StaticPage::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('slug', 'like', '%' . $this->search . '%');
            })
            ->orderBy('sort_order')
            ->orderBy('title')
            ->paginate(10);

        return view('livewire.admin.static-pages-manager', [
            'pages' => $pages
        ]);
    }

    public function createPage()
    {
        $this->authorize('manage_static_pages');
        $this->resetForm();
        $this->showModal = true;
    }

    public function editPage(StaticPage $page)
    {
        $this->authorize('manage_static_pages');
        $this->editingPage = $page;
        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->meta_description = $page->meta_description;
        $this->content = $page->content;
        $this->is_active = $page->is_active;
        $this->show_in_footer = $page->show_in_footer;
        $this->sort_order = $page->sort_order;
        $this->showModal = true;
    }

    public function savePage()
    {
        $this->authorize('manage_static_pages');
        
        // Adjust validation rules for editing
        if ($this->editingPage) {
            $this->rules['slug'] = 'required|string|max:255|regex:/^[a-z0-9-]+$/|unique:static_pages,slug,' . $this->editingPage->id;
        }

        $this->validate();

        if ($this->editingPage) {
            $this->editingPage->update([
                'title' => $this->title,
                'slug' => $this->slug,
                'meta_description' => $this->meta_description,
                'content' => $this->content,
                'is_active' => $this->is_active,
                'show_in_footer' => $this->show_in_footer,
                'sort_order' => $this->sort_order,
                'updated_by' => auth()->id(),
            ]);

            session()->flash('message', 'Page updated successfully!');
        } else {
            StaticPage::create([
                'title' => $this->title,
                'slug' => $this->slug,
                'meta_description' => $this->meta_description,
                'content' => $this->content,
                'is_active' => $this->is_active,
                'show_in_footer' => $this->show_in_footer,
                'sort_order' => $this->sort_order,
                'created_by' => auth()->id(),
            ]);

            session()->flash('message', 'Page created successfully!');
        }

        $this->closeModal();
    }

    public function deletePage(StaticPage $page)
    {
        $this->authorize('manage_static_pages');
        $page->delete();
        session()->flash('message', 'Page deleted successfully!');
    }

    public function toggleActive(StaticPage $page)
    {
        $this->authorize('publish_static_pages');
        $page->update(['is_active' => !$page->is_active]);
        session()->flash('message', 'Page status updated!');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    private function resetForm()
    {
        $this->editingPage = null;
        $this->title = '';
        $this->slug = '';
        $this->meta_description = '';
        $this->content = '';
        $this->is_active = true;
        $this->show_in_footer = true;
        $this->sort_order = 0;
    }
}
