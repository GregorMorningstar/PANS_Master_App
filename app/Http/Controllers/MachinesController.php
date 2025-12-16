<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Inertia\Inertia;
use App\Models\Department;
use App\Models\User;
use App\Enums\MachineStatus;
use Illuminate\Http\Request;


class MachinesController extends Model
{
    public function moderatorIndex()
    {
return Inertia::render('moderator/machines/index');
    }

    public function create()
    {
return Inertia::render('moderator/machines/create', [
    'departments' => Department::select('id','name')->get(),
    'users' => User::select('id','name')->get(),
    'machine_statuses' => array_map(fn($c) => $c->value, MachineStatus::cases()),
]);    }

    public function store(Request $request)
    {
dd($request->all());
    }
}
