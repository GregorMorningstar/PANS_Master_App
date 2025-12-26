<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\Contracts\MachinesServiceInterface;
class MachineFailuresController extends Controller
{


    public function __construct(private readonly MachinesServiceInterface $machineService)
    {
    }

public function index()
    {

        return Inertia::render('machines/failures/index');
     }

public function history()
    {
        return Inertia::render('machines/failures/history');
     }

public function create($machine_id)
    {
        return Inertia::render('machines/failures/create', ['machine_id' => $machine_id]);
     }

    public function show($id)
    {
        // Logic to show a specific machine failure
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'machine_id' => 'required|exists:machines,id',
            'failure_rank' => 'required|integer|min:1|max:10',
            'failure_description' => 'required|string|max:2000',
        ]);
        $data['user_id'] = $request->user()->id;

        $this->machineService->setLastFailureDate((int) $data['machine_id']);
dd($this->machineService);
        return redirect()->route('machines.failures-history');
    }

    public function update(Request $request, $id)
    {
        // Logic to update an existing machine failure record
    }

    public function destroy($id)
    {
        // Logic to delete a machine failure record
    }


}
