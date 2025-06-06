<?php

namespace App\Livewire;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Livewire\Component;
use Illuminate\Support\Str;

class SelfOrder extends Component
{
    public $cart = [];
    public $total = 0;
    public $paymentMethod = 'cash';
    public $showSuccessMessage = false;

    public function mount()
    {
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
    }

    public function calculateTotal()
    {
        $this->total = collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    public function checkout()
    {
        if (empty($this->cart)) {
            return;
        }

        $order = Order::create([
            'customer_id' => null, // For walk-in customers
            'payment_method' => $this->paymentMethod,
            'status' => $this->paymentMethod === 'cash' ? 'paid' : 'pending',
            'total_price' => $this->total,
            'notes' => '',
            'paid_at' => $this->paymentMethod === 'cash' ? now() : null,
        ]);

        foreach ($this->cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);
        }

        $this->cart = [];
        $this->total = 0;
        $this->showSuccessMessage = true;
    }

    public function render()
    {
        return view('livewire.self-order', [
            'categories' => \App\Models\Kategori::with('menu')->get()
        ]);
    }
}