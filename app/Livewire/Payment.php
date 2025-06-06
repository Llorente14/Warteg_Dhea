<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Order;

class Payment extends Component
{
    public $cart = [];
    public $total = 0;
    public $paymentMethod = 'cash';
    public $name = 'Guest User';
    public $email = '';
    public $phone = '';
    public $notes = '';
    public $showQRCode = false;

    public function mount()
    {
        $this->cart = session('cart', []);
        $this->total = session('total', 0);
        $this->notes = session('orderNotes', '');
    }

    public function processPayment()
    {
        $customer = Customer::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'payment_method' => $this->paymentMethod,
            'status' => $this->paymentMethod === 'cash' ? 'paid' : 'pending',
            'total_price' => $this->total,
            'notes' => $this->notes,
            'paid_at' => $this->paymentMethod === 'cash' ? now() : null,
        ]);

        foreach ($this->cart as $item) {
            $order->items()->create([
                'menu_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'notes' => session('itemNotes')[$item['id']] ?? null,
            ]);
        }

        if ($this->paymentMethod !== 'cash') {
            $this->showQRCode = true;
        } else {
            session()->forget(['cart', 'total', 'itemNotes', 'orderNotes']);
            return redirect()->to('/order')->with('success', 'Order placed successfully!');
        }
    }

    public function render()
    {
        return view('livewire.payment');
    }
}
