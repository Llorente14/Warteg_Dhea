<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Order;
// use App\Models\Menu; // Jika Anda perlu mengakses model Menu di sini, import juga

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
    public $orderType = 'dine-in'; // Tambahkan properti untuk menyimpan orderType

    public function mount()
    {
        $this->cart = session('cart', []);
        $this->total = session('total', 0);
        $this->notes = session('orderNotes', '');
        $this->orderType = session('order_type', 'dine-in'); // Ambil orderType dari session
    }

    public function processPayment()
    {
        // Validasi input jika diperlukan
        // $this->validate([
        //     'name' => 'required|string|max:255',
        //     'email' => 'nullable|email|max:255',
        //     'phone' => 'nullable|string|max:20',
        //     'paymentMethod' => 'required|in:cash,qris,bank_transfer',
        //     'notes' => 'nullable|string|max:1000',
        //     'orderType' => 'required|in:dine-in,takeaway', // Tambahkan validasi untuk orderType
        // ]);

        // Buat atau temukan customer
        // Anda mungkin ingin menambahkan logika untuk menemukan customer yang sudah ada berdasarkan email/telepon
        $customer = Customer::firstOrCreate(
            ['email' => $this->email], // Coba temukan berdasarkan email
            [
                'name' => $this->name,
                'phone' => $this->phone,
            ]
        );
        // Jika customer sudah ada tapi namanya "Guest User", update namanya
        if ($customer->wasRecentlyCreated && $this->name === 'Guest User' && !empty($this->email)) {
            $customer->name = $this->name;
            $customer->phone = $this->phone;
            $customer->save();
        } else if (!$customer->wasRecentlyCreated && $customer->name === 'Guest User' && $this->name !== 'Guest User') {
             // Jika customer ditemukan dan namanya masih Guest User, update
             $customer->name = $this->name;
             $customer->phone = $this->phone;
             $customer->save();
        }
        // Atur customer_id dengan ID customer yang baru dibuat atau ditemukan
        $customerId = $customer->id;


        // Buat Order baru
        $order = Order::create([
            'customer_id' => $customerId, // Gunakan ID customer yang telah dibuat/ditemukan
            'payment_method' => $this->paymentMethod,
            'status' => $this->paymentMethod === 'cash' ? 'paid' : 'pending',
            'total_price' => $this->total,
            'notes' => $this->notes,
            'paid_at' => $this->paymentMethod === 'cash' ? now() : null,
            'type' => $this->orderType, // <<< INI YANG PENTING: Mengirim orderType ke tabel Order
        ]);

        // Tambahkan item-item ke order
        foreach ($this->cart as $item) {
            $order->items()->create([
                'menu_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                // Pastikan 'itemNotes' juga disimpan ke session di komponen SelfOrder jika ini digunakan
                'notes' => session('itemNotes')[$item['id']] ?? null, 
            ]);
        }

        // Logika setelah pembayaran
        switch ($this->paymentMethod) {
            case 'cash':
                session()->forget(['cart', 'total', 'itemNotes', 'orderNotes', 'order_type']); // Hapus 'order_type' juga
                session()->flash('message', 'Pembayaran Cash berhasil! Pesanan akan segera diproses');
                return redirect()->to('/order'); // Redirect ke halaman sukses order
                break;

            case 'qris':
                $this->showQRCode = true;
                session()->flash('message', 'Silahkan scan QR Code untuk melakukan pembayaran QRIS');
                // Untuk QRIS, Anda mungkin ingin menyimpan order_id di session atau mengarahkannya ke halaman detail pembayaran dengan ID order
                // session(['last_order_id' => $order->id]); // Contoh
                break;

            case 'bank_transfer':
                session()->flash('message', 'Silahkan transfer ke rekening: BCA 1234567890 a.n. Warteg Dhea');
                // Untuk Bank Transfer, Anda mungkin ingin menyimpan order_id di session
                // session(['last_order_id' => $order->id]); // Contoh
                break;
        }
    }

    public function render()
    {
        return view('livewire.payment');
    }
}