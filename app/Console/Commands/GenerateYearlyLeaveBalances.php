<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\LeaveBalance;
use Carbon\Carbon;

class GenerateYearlyLeaveBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leave:generate-yearly {--year=} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate yearly leave balances for all users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $year = $this->option('year') ?: date('Y');
        $force = $this->option('force');
        
        $this->info("Generating leave balances for year: {$year}");

        // Sprawdź czy już istnieją rekordy dla tego roku
        $existingCount = LeaveBalance::where('year', $year)->count();
        
        if ($existingCount > 0 && !$force) {
            if (!$this->confirm("Found {$existingCount} existing records for year {$year}. Continue anyway?")) {
                $this->info('Operation cancelled.');
                return;
            }
        }

        $users = User::whereNotNull('email_verified_at')->get();
        $created = 0;
        $skipped = 0;

        $progressBar = $this->output->createProgressBar($users->count());
        $progressBar->start();

        foreach ($users as $user) {
            // Sprawdź czy użytkownik już ma balance na ten rok
            $existingBalance = LeaveBalance::where('user_id', $user->id)
                ->where('year', $year)
                ->where('leave_type', 'vacation')
                ->first();

            if ($existingBalance && !$force) {
                $skipped++;
                $progressBar->advance();
                continue;
            }

            // Oblicz lata stażu na początek roku
            if ($user->employment_start_date) {
                $employmentStart = Carbon::parse($user->employment_start_date);
                $yearStart = Carbon::createFromDate($year, 1, 1);
                $seniorityYears = $employmentStart->diffInYears($yearStart);
            } else {
                // Jeśli brak daty zatrudnienia, użyj daty utworzenia konta
                $seniorityYears = Carbon::parse($user->created_at)->diffInYears(Carbon::createFromDate($year, 1, 1));
            }

            // Determine entitlement based on seniority
            $entitlementDays = $seniorityYears >= 10 ? 24 : 20;

            // Utwórz lub zaktualizuj balance
            $leaveBalance = LeaveBalance::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'year' => $year,
                    'leave_type' => 'vacation'
                ],
                [
                    'entitlement_days' => $entitlementDays,
                    'used_days' => 0,
                    'remaining_days' => $entitlementDays,
                    'seniority_years' => $seniorityYears,
                    'employment_start_date' => $user->employment_start_date ?? $user->created_at,
                ]
            );

            $created++;
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        $this->info("✅ Successfully processed {$users->count()} users:");
        $this->info("   - Created/Updated: {$created}");
        $this->info("   - Skipped: {$skipped}");
    }
}
