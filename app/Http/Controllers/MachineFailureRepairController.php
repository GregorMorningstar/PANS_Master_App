<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Enums\MachineFailureRepairsStatus;
use App\Models\MachineFailureRepair;
use App\Models\MachineFailure;
use App\Services\Contracts\MachineFailureRepairServiceInterface;
class MachineFailureRepairController extends Controller
{



public function __construct(private readonly MachineFailureRepairServiceInterface $machineFailureRepairService)
    {
    }
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


        $data = $request->validate([
            'machine_failure_id' => ['required','integer','exists:machine_failures,id'],
            'status' => ['required','string'],
            'cost' => ['nullable','numeric'],
            'description' => ['nullable','string'],
            'started_at' => ['nullable','date'],
            'finished_at' => ['nullable','date'],
        ]);
        $repair = MachineFailureRepair::create($data);
        if($repair){
            try{
                $machineFailure = $this->machineFailureRepairService->getFailureMachineWithMachine($repair->machine_failure_id);
                $machineFailure->repair_order_no = 'RO-' . $repair->barcode . '/' . date('d-m-Y');
                $this->machineFailureRepairService->update($repair->id, ['repair_order_no' => $machineFailure->repair_order_no]);

                  return redirect()->route('machines.failures.repairs.create', ['machine_id' => $repair->machine_failure_id])
            ->with('success', 'Naprawa procesu naprawy maszyny  '.$repair->machine_failure_id.' zapisana');
            } catch (\Exception $e) {
                return redirect()->route('machines.failures.repairs.create', ['machine_id' => $repair->machine_failure_id])
            ->with('error', 'Dodanie zlecenia naprawy maszyny o id '.$repair->machine_failure_id.' nie powiedlo sie z powodu : ' . $e->getMessage());
                }
        }



    }

    public function createRaportedFailureNextStep(Request $request, $id)
    {
        $machineFailureRepair = $this->machineFailureRepairService->getFailureMachineWithMachine($id);
        $statuses = MachineFailureRepairsStatus::toSelectArray();

        return Inertia::render('machines/failures/repairs/create_nextstep', [
            'machineFailureRepair' => $machineFailureRepair,
            'statuses' => $statuses,
        ]);
    }

    public function destroy($id)
    {
        try {
            $this->machineFailureRepairService->delete($id);
            return redirect()->back()->with('success', 'Rekord naprawy maszyny został pomyślnie usunięty.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Usunięcie rekordu naprawy maszyny nie powiodło się: ' . $e->getMessage());
        }
    }
}

