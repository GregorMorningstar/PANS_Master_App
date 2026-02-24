<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\Contracts\OrderServiceInterface;

class OrderController extends Controller
{

public function __construct(private readonly OrderServiceInterface $orderService)
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

   }
