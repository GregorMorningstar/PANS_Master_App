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
         return Inertia::render('moderator/production-planning/element/index');
    }
    public function processes(Request $request)
    {
         return Inertia::render('moderator/production-planning/processes/index');


    }
}
