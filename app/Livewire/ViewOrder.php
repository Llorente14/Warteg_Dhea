<?php

namespace App\Livewire;

use Livewire\Component;

class ViewOrder extends Component
{
    public $cart = [];
    public $total = 0;
    public $notes = '';
    public $itemNotes = [];
    public $orderType = 'dine-in'; // <<< TAMBAHKAN PROPERTI INI
    
    protected $queryString = [];
    protected $listeners = ['refreshComponent' => '$refresh'];
    public $isProcessing = false;

    public function mount()
    {
        // Get cart data from session
        $this->cart = session('cart', []);
        $this->total = session('total', 0);
        $this->itemNotes = session('itemNotes', []);
        
        // <<< MUAT order_type DARI SESSION DI SINI
        $this->orderType = session('order_type', 'dine-in'); 
    }

    // ... (metode-metode lain seperti incrementQuantity, decrementQuantity, removeItem, updateCart, addNote, updateGeneralNote tidak berubah)

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

    public function removeItem($menuId)
    {
        if ($this->isProcessing) return;
        $this->isProcessing = true;

        try {
            if (isset($this->cart[$menuId])) {
                unset($this->cart[$menuId]); // Hapus item dari array cart
                unset($this->itemNotes[$menuId]); // Hapus juga catatan item terkait (jika ada)

                $this->updateCart(); // Perbarui total dan simpan ke sesi

                // Opsional: Kirim notifikasi ke frontend
                $this->dispatch('itemRemoved', $menuId);
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
        
        // <<< PASTIKAN ORDER_TYPE JUGA DISIMPAN JIKA ADA PERUBAHAN DI HALAMAN INI (misalnya jika view-order juga bisa mengubah type)
        // Saat ini, order_type hanya dibaca, jadi tidak perlu disimpan ulang di sini kecuali ada interaksi di view-order
        session()->put([
            'cart' => $this->cart,
            'total' => $this->total
            // 'order_type' => $this->orderType // Hanya tambahkan ini jika view-order punya UI untuk mengubah type
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