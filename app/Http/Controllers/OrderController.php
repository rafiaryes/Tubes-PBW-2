<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {

        DB::beginTransaction(); // Start the transaction
        try {
            // Get form data
            $paymentMethod = $request['payment_method'];
            $orderMethod = $request['order_method'];
            $cart = $request['cart']; // It's already validated as JSON
            $totalPrice = $request['total_price'];

            // You can replace these with actual logic for order creation
            $order = Order::create([
                'payment_method' => $paymentMethod,
                'order_method' => $orderMethod,
                'cart' => $cart,
                'total_price' => $totalPrice,
                'status' => 'pending',
            ]);

            // Example: Insert order items (assuming cart is an array of items)
            foreach (json_decode($cart) as $item) {
                $menu = Menu::find($item->id_menu);

                if (!$menu) {
                    continue;
                }
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $item->id_menu,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ]);
            }

            // Commit the transaction
            DB::commit();

            // Flash success message and redirect
            session()->flash('success', 'Berhasil membuat order!');
            session()->flash('id_order', $order->id);
            return redirect()->route('user.home'); // Redirect to a success page
        } catch (\Exception $e) {
            dd($e);
            // Rollback the transaction in case of an error
            DB::rollback();

            // Flash error message and redirect back
            session()->flash('error', 'Gagal membuat order. Tolong coba lagi.');
            return back();
        }
    }
}
