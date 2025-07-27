<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Component;

class ForumsIndex extends Component
{
    public function render()
    {
        $categories = Category::getForumsIndex();
        
        return view('livewire.forums-index', [
            'categories' => $categories
        ])->title('Forums');
    }
}
