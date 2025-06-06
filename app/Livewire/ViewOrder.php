<?php

namespace App\Livewire;

use Livewire\Component;

class ViewOrder extends Component
{
    public $cart = [];
    public $total = 0;
    public $notes = '';
    public $itemNotes = [];

    public function mount()
    {
        // Get cart data from session
        $this->cart = session('cart', []);
        $this->total = session('total', 0);
        $this->itemNotes = session('itemNotes', []);
    }

    public function addNote($menuId, $note)
    {
        $this->itemNotes[$menuId] = $note;
        session(['itemNotes' => $this->itemNotes]);
    }

    public function updateGeneralNote()
    {
        session(['orderNotes' => $this->notes]);
    }

    public function render()
    {
        return view('livewire.view-order');
    }
}
