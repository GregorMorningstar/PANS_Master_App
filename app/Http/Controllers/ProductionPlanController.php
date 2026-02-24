<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductionPlanController extends Controller
{

public function __construct()
    {
    }

    public function index(Request $request)
    {
         // Load production schemas with pagination
         $schemas = \App\Models\ProductionSchema::with(['item','steps','steps.machine','steps.operation','steps.material'])
             ->orderBy('id', 'desc')
             ->paginate(10)
             ->appends($request->query());

           return Inertia::render('moderator/production-planning/element/index', [
                'schemas' => $schemas,
           ]);
    }
    public function processes(Request $request)
    {
         return Inertia::render('moderator/production-planning/processes/index');


    }
}
