<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
class ModeratorController extends Controller
{

    public function dashboard()
    {
       return Inertia::render('moderator/dashboard/index');
    }

}
