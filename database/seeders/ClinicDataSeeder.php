<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ClinicDataSeeder extends Seeder
{
    public function run(): void
    {
        $patients = collect([
            ['first_name' => 'Sophie', 'last_name' => 'Jansen', 'email' => 'sophie.jansen@smilepro.test', 'phone' => '0612345678', 'date_of_birth' => '1992-03-14', 'last_visit_at' => Carbon::now()->subDays(12)],
            ['first_name' => 'Lars', 'last_name' => 'Visser', 'email' => 'lars.visser@smilepro.test', 'phone' => '0623456789', 'date_of_birth' => '1987-07-01', 'last_visit_at' => Carbon::now()->subDays(35)],
            ['first_name' => 'Mila', 'last_name' => 'Bakker', 'email' => 'mila.bakker@smilepro.test', 'phone' => '0634567890', 'date_of_birth' => '1998-11-22', 'last_visit_at' => Carbon::now()->subDays(5)],
            ['first_name' => 'Noah', 'last_name' => 'van Leeuwen', 'email' => 'noah.leeuwen@smilepro.test', 'phone' => '0645678901', 'date_of_birth' => '1990-01-08', 'last_visit_at' => Carbon::now()->subDays(60)],
        ]);

        DB::table('patients')->upsert($patients->map(function ($patient) {
            return array_merge($patient, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        })->toArray(), ['email']);

        $patientMap = DB::table('patients')->pluck('id', 'email');

        $staff = User::whereIn('email', [
            'tandarts1@smilepro.test',
            'mondhygienist1@smilepro.test',
            'assistent1@smilepro.test',
            'praktijkmanagement1@smilepro.test',
        ])->get();

        $availabilities = [];
        foreach ($staff as $member) {
            foreach (range(0, 4) as $offset) {
                $availabilities[] = [
                    'user_id' => $member->id,
                    'available_on' => Carbon::now()->addDays($offset)->toDateString(),
                    'slot' => ['08:00-10:00', '10:00-12:00', '13:00-15:00'][array_rand([0, 1, 2])],
                    'status' => collect(['open', 'booked', 'blocked'])->random(),
                    'notes' => 'Automatisch gegenereerde beschikbaarheid',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        DB::table('availabilities')->insert($availabilities);

        $appointments = [];
        foreach ($patients as $patient) {
            $patientId = $patientMap[$patient['email']];
            $staffMember = $staff->random();
            $appointments[] = [
                'patient_id' => $patientId,
                'staff_id' => $staffMember->id,
                'scheduled_at' => Carbon::now()->addDays(rand(-5, 10))->setTime(rand(8, 16), 0),
                'status' => collect(['gepland', 'bezig', 'afgerond', 'geannuleerd'])->random(),
                'treatment_type' => collect(['Controle', 'Vulling', 'Reiniging', 'Consult'])->random(),
                'notes' => 'Gepland tijdens seed run',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('appointments')->insert($appointments);

        $invoices = [];
        foreach ($patients as $patient) {
            $patientId = $patientMap[$patient['email']];
            $invoices[] = [
                'patient_id' => $patientId,
                'invoice_number' => 'INV-' . strtoupper(substr($patient['last_name'], 0, 3)) . rand(1000, 9999),
                'total_amount' => rand(5000, 25000) / 100,
                'issue_date' => Carbon::now()->subDays(rand(1, 20)),
                'due_date' => Carbon::now()->addDays(rand(5, 20)),
                'status' => collect(['open', 'betaald', 'verlopen'])->random(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('invoices')->insert($invoices);

        $messages = [];
        foreach ($patients as $patient) {
            $patientId = $patientMap[$patient['email']];
            $messages[] = [
                'patient_id' => $patientId,
                'subject' => 'Vraag over behandeling',
                'body' => 'Kunt u bevestigen welke nazorg nodig is?',
                'status' => collect(['nieuw', 'gelezen', 'opgelost'])->random(),
                'received_at' => Carbon::now()->subHours(rand(4, 72)),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('messages')->insert($messages);
    }
}
