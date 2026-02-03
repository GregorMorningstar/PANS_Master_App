<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Enums\MachineFailureRepairsStatus;
use App\Models\MachineFailureRepair;
use App\Models\MachineFailure;

class MachineFailureRepairController extends Controller
{
    public function createRaportedFailure(Request $request)
    {
        $machineId = (int)$request->get('machine_id');
        $machineFailure = MachineFailure::with(['machine', 'user'])->find($machineId);
        $statuses = MachineFailureRepairsStatus::toSelectArray();

        return Inertia::render('machines/failures/repairs/create', [
            'machineFailure' => $machineFailure,
            'statuses' => $statuses,
        ]);
    }

    public function store(Request $request)
    {


    dd($request->all());
        $data = $request->validate([
            'machine_failure_id' => ['required','integer','exists:machine_failures,id'],
            'status' => ['required','string'],
            'cost' => ['nullable','numeric'],
            'description' => ['nullable','string'],
            'started_at' => ['nullable','date'],
            'finished_at' => ['nullable','date'],
        ]);

        $repair = MachineFailureRepair::create([
            'machine_failure_id' => $data['machine_failure_id'],
            'status' => $data['status'] ?? MachineFailureRepairsStatus::REPORTED->value,
            'cost' => $data['cost'] ?? null,
            'description' => $data['description'] ?? null,
            'started_at' => $data['started_at'] ?? null,
            'finished_at' => $data['finished_at'] ?? null,
        ]);

        return redirect()->route('machines.failures.repairs.create', ['machine_id' => $repair->machine_failure_id])
            ->with('success', 'Naprawa zapisana');
    }
}

