<?php

namespace App\Livewire;

use Livewire\Component;

class ViewOrder extends Component
{
    public $cart = [];
    public $total = 0;
    public $notes = '';
    public $itemNotes = [];
    protected $queryString = [];
    protected $listeners = ['refreshComponent' => '$refresh'];
    // Add property to prevent duplicate requests
    public $isProcessing = false;

    public function mount()
    {
        // Get cart data from session
        $this->cart = session('cart', []);
        $this->total = session('total', 0);
        $this->itemNotes = session('itemNotes', []);
    }

 

    // Optimize increment with debouncing
    public function incrementQuantity($menuId)
    {
        if ($this->isProcessing) return;
        $this->isProcessing = true;

        try {
            if (isset($this->cart[$menuId])) {
                $this->cart[$menuId]['quantity']++;
                $this->updateCart();
            }
        } finally {
            $this->isProcessing = false;
        }
    }

    // Optimize decrement with debouncing
    public function decrementQuantity($menuId)
    {
        if ($this->isProcessing) return;
        $this->isProcessing = true;

        try {
            if (isset($this->cart[$menuId]) && $this->cart[$menuId]['quantity'] > 1) {
                $this->cart[$menuId]['quantity']--;
                $this->updateCart();
            }
        } finally {
            $this->isProcessing = false;
        }
    }

    // Optimize cart updates
    private function updateCart()
    {
        $this->total = collect($this->cart)
            ->sum(fn($item) => $item['price'] * $item['quantity']);
        
        session()->put([
            'cart' => $this->cart,
            'total' => $this->total
        ]);
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
