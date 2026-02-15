<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductionMaterialController extends Controller
{
    public function index()
    {
      return Inertia::render('moderator/product_material/index');
    }
}
