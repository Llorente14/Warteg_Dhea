<?php

namespace App\Livewire;

use App\Models\Menu;
use Livewire\Component;

class SelfOrder extends Component
{
    public $cart = [];
    public $total = 0;

    public function mount()
    {
        $this->cart = session('cart', []);
        $this->calculateTotal();
    }

    public function addToCart($menuId)
    {
        if (isset($this->cart[$menuId])) {
            $this->cart[$menuId]['quantity']++;
        } else {
            $menu = Menu::find($menuId);
            $this->cart[$menuId] = [
                'id' => $menu->id,
                'name' => $menu->name,
                'price' => $menu->price,
                'quantity' => 1
            ];
        }
        
        $this->calculateTotal();
        $this->updateSession();
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
    }

    public function calculateTotal()
    {
        $this->total = collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    private function updateSession()
    {
        session(['cart' => $this->cart, 'total' => $this->total]);
    }

    public function viewCart()
    {
        return redirect()->to('/view-orders');
    }

    public function render()
    {
        return view('livewire.self-order', [
            'categories' => \App\Models\Kategori::with('menu')->get()
        ]);
    }
}
