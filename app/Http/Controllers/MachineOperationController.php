<?php



namespace App\Http\Controllers;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\OperationMachineService;
use App\Services\MachineService;
class MachineOperationController extends Controller
{

    public function __construct()
    {
    }


    public function index()
    {
        return Inertia::render('moderator/machines/operations/index');
        // Logika wyświetlania listy operacji maszyn
    }
    public function createOperation($machine_id)
    {
        return Inertia::render('moderator/machines/operations/create', [
            'machine_id' => $machine_id,
        ]);
        // Logika wyświetlania formularza dodawania operacji maszyny
    }

    public function storeOperation(Request $request, $machine_id)
    {
        dd($request->all(), $machine_id);
    }
}
