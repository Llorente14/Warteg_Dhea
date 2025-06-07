<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Menu;

use App\Models\Kategori;

class SelfOrder extends Component
{
    public $orderType = 'dine-in';
    public $cart = [];
    public $total = 0;
    public $selectedItem = null;
    public $quantity = 1;

    /**
     * Metode mount() dijalankan saat komponen Livewire diinisialisasi.
     * Memuat data keranjang, total, dan tipe pesanan dari session.
     */
    public function mount()
    {
        $this->cart = session('cart', []);
        $this->total = session('total', 0);
        $this->orderType = session('order_type', 'dine-in'); 
        
        // <<< PENTING: Panggil updateSession() setelah mount untuk memastikan
        // status awal (termasuk orderType dari session) selalu disimpan kembali
        // ke session jika ada perubahan yang terjadi sebelum interaksi lain.
        $this->updateSession(); 
    }

    /**
     * Hook Livewire yang dipanggil otomatis saat properti $orderType berubah.
     * Ini akan memastikan orderType selalu disimpan ke session.
     */
    public function updatedOrderType($value)
    {
        $this->updateSession(); // Panggil metode untuk menyimpan state ke session
        // Opsional: Anda bisa menambahkan flash message di sini jika ingin notifikasi real-time
        // session()->flash('message', 'Tipe pesanan diubah menjadi: ' . ($value === 'dine-in' ? 'Dine-in' : 'Takeaway'));
    }

    public function selectItem($menuId)
    {
        $this->selectedItem = $menuId;
        $this->quantity = 1;
    }

    public function incrementQty()
    {
        $this->quantity++;
    }

    public function decrementQty()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCartWithQty()
    {
        if (isset($this->cart[$this->selectedItem])) {
            $this->cart[$this->selectedItem]['quantity'] += $this->quantity;
        } else {
            $menu = Menu::find($this->selectedItem);
            if ($menu) {
                $this->cart[$this->selectedItem] = [
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'price' => $menu->price,
                    'quantity' => $this->quantity
                ];
            } else {
                session()->flash('message', 'Item tidak ditemukan.');
                return;
            }
        }
        
        $this->calculateTotal();
        $this->updateSession();
        $this->selectedItem = null;
        $this->quantity = 1;
        session()->flash('message', 'Item berhasil ditambahkan ke keranjang!');
    }

    public function removeFromCart($menuId)
    {
        if (isset($this->cart[$menuId])) {
            if ($this->cart[$menuId]['quantity'] > 1) {
                $this->cart[$menuId]['quantity']--;
            } else {
                unset($this->cart[$menuId]);
            }
        }
        
        $this->calculateTotal();
        $this->updateSession();
        session()->flash('message', 'Item berhasil diperbarui di keranjang!');
    }

    public function calculateTotal()
    {
        $this->total = collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    private function updateSession()
    {
        session([
            'cart' => $this->cart,
            'total' => $this->total,
            'order_type' => $this->orderType // Ini akan selalu menyimpan orderType terbaru
        ]);
    }

    public function viewCart()
    {
        return redirect()->to('/view-orders');
    }

    public function render()
    {
        return view('livewire.self-order', [
            'categories' => Kategori::with(['menu' => function($query) {
                $query->where('is_available', true);
            }])->get()
        ]);
    }
}