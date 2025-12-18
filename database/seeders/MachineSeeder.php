<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Machines;
use App\Models\User;
use App\Models\Department; // <- poprawka: Department zamiast Departments
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MachineSeeder extends Seeder
{
    public function run(): void
    {
        // upewnij się, że link storage jest utworzony: php artisan storage:link
        Storage::disk('public')->makeDirectory('machines');

        // pobierz dostępnych userów i departamenty do przypisania
        $userIds = User::pluck('id')->toArray();
        $departmentIds = Department::pluck('id')->toArray(); // <- poprawka: Department zamiast Departments

        // stwórz 10 maszyn, pobierz obrazki i zapisz ścieżkę w image_path
        Machines::factory()->count(10)->create()->each(function (Machines $machine, $i) use ($userIds, $departmentIds) {
            // przypisz losowego usera i department jeśli dostępne
            if (!empty($userIds)) {
                $machine->user_id = fake()->optional(0.7)->randomElement($userIds);
            }
            if (!empty($departmentIds)) {
                $machine->department_id = fake()->optional(0.8)->randomElement($departmentIds);
            }

            $seed = 'machine-' . ($machine->id ?? Str::random(6));
            // używamy picsum (wolno losowe obrazy). Możesz zmienić URL na inny placeholder
            $url = "https://picsum.photos/seed/{$seed}/600/400";

            try {
                $resp = Http::timeout(15)->get($url);
                if ($resp->ok()) {
                    $filename = 'machines/' . $seed . '.jpg';
                    Storage::disk('public')->put($filename, $resp->body());
                    $machine->image_path = $filename;
                }
            } catch (\Throwable $e) {
                // ignoruj błąd pobierania — maszyna pozostanie bez obrazka
                \Log::warning("Nie udało się pobrać obrazka dla maszyny {$machine->id}: " . $e->getMessage());
            }

            $machine->save();
        });

        $this->command->info('Utworzono 10 maszyn z obrazkami.');
    }
}
