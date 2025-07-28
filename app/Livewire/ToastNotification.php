<?php

namespace App\Livewire;

use Livewire\Component;

class ToastNotification extends Component
{
    public $show = false;
    public $message = '';
    public $type = 'success'; // success, error, warning, info
    
    protected $listeners = [
        'showToast' => 'show'
    ];
    
    public function show($data)
    {
        $this->message = $data['message'] ?? '';
        $this->type = $data['type'] ?? 'success';
        $this->show = true;
        
        // Auto-hide after 5 seconds
        $this->dispatch('hideToast')->delay(5000);
    }
    
    public function hide()
    {
        $this->show = false;
        $this->message = '';
    }
    
    protected $listeners_auto = [
        'hideToast' => 'hide'
    ];

    public function render()
    {
        return view('livewire.toast-notification');
    }
}
