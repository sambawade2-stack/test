<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Payment;
use App\Models\Payroll;
use App\Models\SchoolYear;
use App\Models\Staff;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Utilisateurs ─────────────────────────────────────────────────────

        $admin = User::firstOrCreate(['email' => 'admin@ecole.sn'], [
            'name'      => 'Administrateur',
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        User::firstOrCreate(['email' => 'comptable@ecole.sn'], [
            'name'      => 'Fatou Diallo',
            'password'  => Hash::make('password'),
            'role'      => 'comptable',
            'is_active' => true,
        ]);

        User::firstOrCreate(['email' => 'directeur@ecole.sn'], [
            'name'      => 'Moussa Ndiaye',
            'password'  => Hash::make('password'),
            'role'      => 'directeur',
            'is_active' => true,
        ]);

        // ─── Année scolaire ───────────────────────────────────────────────────

        $year = SchoolYear::firstOrCreate(['name' => '2024-2025'], [
            'start_date'      => '2024-10-01',
            'end_date'        => '2025-07-15',
            'is_active'       => true,
            'inscription_fee' => 25000,
            'monthly_fee'     => 15000,
        ]);

        // ─── Classes ──────────────────────────────────────────────────────────

        $levels = [
            ['level' => '6ème',      'section' => 'A', 'cycle' => 'collège'],
            ['level' => '6ème',      'section' => 'B', 'cycle' => 'collège'],
            ['level' => '5ème',      'section' => 'A', 'cycle' => 'collège'],
            ['level' => '4ème',      'section' => 'A', 'cycle' => 'collège'],
            ['level' => '3ème',      'section' => 'A', 'cycle' => 'collège'],
            ['level' => '2nde',      'section' => 'A', 'cycle' => 'lycée'],
            ['level' => '1ère',      'section' => 'S', 'cycle' => 'lycée'],
            ['level' => '1ère',      'section' => 'L', 'cycle' => 'lycée'],
            ['level' => 'Terminale', 'section' => 'S', 'cycle' => 'lycée'],
            ['level' => 'Terminale', 'section' => 'L', 'cycle' => 'lycée'],
        ];

        $classrooms = [];
        foreach ($levels as $l) {
            $classrooms[] = Classroom::firstOrCreate(
                ['level' => $l['level'], 'section' => $l['section'], 'school_year_id' => $year->id],
                ['name' => trim($l['level'] . ' ' . $l['section']), 'cycle' => $l['cycle'], 'max_students' => 45]
            );
        }

        // ─── Enseignants ──────────────────────────────────────────────────────

        $teachersData = [
            ['first_name' => 'Ibrahima', 'last_name' => 'Sow',   'subject' => 'Mathématiques', 'gender' => 'M', 'base_salary' => 180000],
            ['first_name' => 'Aminata',  'last_name' => 'Ba',    'subject' => 'Français',      'gender' => 'F', 'base_salary' => 170000],
            ['first_name' => 'Mamadou',  'last_name' => 'Fall',  'subject' => 'Sciences',      'gender' => 'M', 'base_salary' => 175000],
            ['first_name' => 'Rokhaya',  'last_name' => 'Diop',  'subject' => 'Histoire-Géo',  'gender' => 'F', 'base_salary' => 165000],
            ['first_name' => 'Cheikh',   'last_name' => 'Thiam', 'subject' => 'Anglais',       'gender' => 'M', 'base_salary' => 160000],
        ];

        foreach ($teachersData as $i => $t) {
            $email = strtolower($t['first_name']) . '.' . strtolower($t['last_name']) . '@ecole.sn';
            Teacher::firstOrCreate(['email' => $email], [
                ...$t,
                'employee_number' => sprintf('ENS-%05d', $i + 1),
                'hire_date'       => '2020-09-01',
                'nationality'     => 'Sénégalaise',
                'status'          => 'active',
                'contract_type'   => 'permanent',
            ]);
        }

        // ─── Personnel ────────────────────────────────────────────────────────

        $staffData = [
            ['first_name' => 'Ndéye',    'last_name' => 'Diallo', 'category' => 'administratif', 'position' => 'Secrétaire',         'gender' => 'F', 'base_salary' => 90000],
            ['first_name' => 'Ousmane',  'last_name' => 'Sarr',   'category' => 'appoint',       'position' => 'Gardien',             'gender' => 'M', 'base_salary' => 75000],
            ['first_name' => 'Dieynaba', 'last_name' => 'Kane',   'category' => 'appoint',       'position' => "Agent d'entretien",   'gender' => 'F', 'base_salary' => 70000],
        ];

        foreach ($staffData as $i => $s) {
            $prefix = $s['category'] === 'administratif' ? 'ADM' : 'APP';
            $email  = strtolower($s['first_name']) . '.' . strtolower($s['last_name']) . '@ecole.sn';
            Staff::firstOrCreate(['email' => $email], [
                ...$s,
                'employee_number' => sprintf('%s-%05d', $prefix, $i + 1),
                'hire_date'       => '2021-10-01',
                'nationality'     => 'Sénégalaise',
                'status'          => 'active',
                'contract_type'   => 'permanent',
            ]);
        }

        // ─── Élèves (10 par classe) ───────────────────────────────────────────

        $firstNames = ['Alioune', 'Binta', 'Cheikhouna', 'Dieynaba', 'El Hadji', 'Fatoumata', 'Gora', 'Hawa', 'Ismaïla', 'Khady'];
        $lastNames  = ['Ndiaye', 'Diallo', 'Fall', 'Sow', 'Ba', 'Sarr', 'Diop', 'Mbaye', 'Faye', 'Gueye'];

        $studentIndex = 1;
        foreach ($classrooms as $classroom) {
            for ($i = 0; $i < 10; $i++) {
                $regNum = sprintf('STU-2024-%05d', $studentIndex);
                $fn     = $firstNames[($studentIndex - 1) % 10];
                $ln     = $lastNames[$i % 10];
                $gender = $i % 2 === 0 ? 'M' : 'F';

                $student = Student::firstOrCreate(['registration_number' => $regNum], [
                    'first_name'      => $fn,
                    'last_name'       => $ln,
                    'date_of_birth'   => now()->subYears(rand(12, 20))->format('Y-m-d'),
                    'place_of_birth'  => 'Dakar',
                    'gender'          => $gender,
                    'nationality'     => 'Sénégalaise',
                    'parent_name'     => "Parent de $fn $ln",
                    'parent_phone'    => '+221 7' . rand(0, 9) . ' ' . rand(100, 999) . ' ' . rand(10, 99) . ' ' . rand(10, 99),
                    'parent_email'    => "parent.{$fn}.{$ln}@gmail.com",
                    'parent_relation' => $gender === 'M' ? 'père' : 'mère',
                    'classroom_id'    => $classroom->id,
                    'school_year_id'  => $year->id,
                    'enrolled_at'     => '2024-10-01',
                    'is_active'       => true,
                ]);

                // Paiement inscription pour 80% des élèves
                if ($studentIndex % 5 !== 0) {
                    Payment::firstOrCreate(
                        ['student_id' => $student->id, 'payment_type' => 'inscription', 'school_year_id' => $year->id, 'month' => null, 'year' => null],
                        [
                            'receipt_number' => sprintf('INS-202410-%05d', $studentIndex),
                            'classroom_id'   => $classroom->id,
                            'amount_due'     => $year->inscription_fee,
                            'amount_paid'    => $year->inscription_fee,
                            'balance'        => 0,
                            'status'         => 'paid',
                            'payment_method' => 'cash',
                            'payment_date'   => '2024-10-01',
                            'created_by'     => $admin->id,
                        ]
                    );
                }

                // Mensualités Oct-Déc pour 75% des élèves
                foreach ([10, 11, 12] as $m) {
                    if ($studentIndex % 4 !== 0) {
                        Payment::firstOrCreate(
                            ['student_id' => $student->id, 'payment_type' => 'mensualite', 'month' => $m, 'year' => 2024, 'school_year_id' => $year->id],
                            [
                                'receipt_number' => sprintf('MEN-2024%02d-%05d', $m, $studentIndex),
                                'classroom_id'   => $classroom->id,
                                'amount_due'     => $year->monthly_fee,
                                'amount_paid'    => $year->monthly_fee,
                                'balance'        => 0,
                                'status'         => 'paid',
                                'payment_method' => 'cash',
                                'payment_date'   => "2024-{$m}-05",
                                'created_by'     => $admin->id,
                            ]
                        );
                    }
                }

                $studentIndex++;
            }
        }

        // ─── Fiches de paie (octobre 2024) ────────────────────────────────────

        $teachers = Teacher::all();
        foreach ($teachers as $i => $teacher) {
            Payroll::firstOrCreate(
                ['payable_id' => $teacher->id, 'payable_type' => Teacher::class, 'month' => 10, 'year' => 2024],
                [
                    'payroll_number' => sprintf('PAY-202410-%05d', $i + 1),
                    'base_salary'    => $teacher->base_salary,
                    'bonuses'        => 0,
                    'deductions'     => 0,
                    'net_salary'     => $teacher->base_salary,
                    'status'         => 'paid',
                    'payment_date'   => '2024-10-28',
                    'payment_method' => 'virement',
                    'created_by'     => $admin->id,
                ]
            );
        }

        $this->command->info('');
        $this->command->info('✅  Base de données peuplée avec succès !');
        $this->command->info('   → Admin     : admin@ecole.sn     / password');
        $this->command->info('   → Comptable : comptable@ecole.sn / password');
        $this->command->info('   → Directeur : directeur@ecole.sn / password');
    }
}
