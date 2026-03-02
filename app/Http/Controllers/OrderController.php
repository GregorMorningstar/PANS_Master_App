<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\Contracts\OrderServiceInterface;
use App\Services\Contracts\ItemsFinishedGoodServiceInterface;

class OrderController extends Controller
{

public function __construct(private readonly OrderServiceInterface $orderService,
                            private readonly ItemsFinishedGoodServiceInterface $itemsFinishedGoodService
)
    {

    }


public function index(Request $request)
    {

$order = $this->orderService->getActiveOrdersPaginated(15, $request->all());
//dd($order);
     return Inertia::render('orders/index', [
            'orders' => $order



        ]);
    }

    public function create()
    {
        return Inertia::render('orders/create');
    }

    public function show($id)
    {
        $order = $this->orderService->find($id);
        if (!$order) {
            abort(404, 'Zamówienie nie znalezione');
        }
        // Pobierz produkty zamówienia
        $items = $order->items()->with('product')->get();
        return Inertia::render('orderItems/show', [
            'order' => $order,
            'items' => $items,
        ]);
    }
    public function store(Request $request)
    {


        $validatedData = $request->validate([
            'customer_name' => 'required|string|max:255',
            'planned_production_at' => 'nullable|string|max:20',
            'finished_at' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:255',
        ]);
    //dd($validatedData);
        $order = $this->orderService->create($validatedData);

        // Po utworzeniu zamówienia przekieruj na stronę szczegółów zamówienia
        return redirect()->route('moderator.orders.show', ['id' => $order->id])->with('success', 'Zamówienie zostało utworzone pomyślnie.');
    }

    public function addItem($id)
    {
        $order = $this->orderService->find($id);
        $products = $this->itemsFinishedGoodService->paginate(100, []);
        if (!$order) {
            abort(404, 'Zamówienie nie znalezione');
        }
        return Inertia::render('orderItems/item/create-one-items', [
            'order' => $order,
            'availableProducts' => $products->items(), ]);
    }
}
