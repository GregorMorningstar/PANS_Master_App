<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;


class ItemsFinishedGoodController extends Controller
{
    public function __construct()
    {
    }

    public function index(Request $request)
    {
         return Inertia::render('items/index');
    }

    public function create(Request $request)
    {
         return Inertia::render('items/create');
    }
}
