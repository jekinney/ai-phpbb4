<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Livewire\HomePage;

class HomeController extends Controller
{
    public function index()
    {
        $component = new HomePage();
        return $component->render();
    }
}
