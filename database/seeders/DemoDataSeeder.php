<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use App\Models\MedicalRecord;
use App\Models\ClinicalGuideline;
use App\Models\User;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        // Create demo patients
        $patients = [
            [
                'patient_id' => 'P1001',
                'first_name' => 'John',
                'last_name' => 'Smith',
                'date_of_birth' => '1980-05-15',
                'gender' => 'male',
                'email' => 'john.smith@email.com',
                'phone' => '555-0123',
                'address' => '123 Health St, Medical City',
                'emergency_contact' => 'Jane Smith',
                'blood_type' => 'A+',
                'medical_history' => 'Hypertension, Type 2 Diabetes',
                'allergies' => 'Penicillin, Shellfish'
            ],
            [
                'patient_id' => 'P1002',
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'date_of_birth' => '1992-08-23',
                'gender' => 'female',
                'email' => 'sarah.j@email.com',
                'phone' => '555-0124',
                'address' => '456 Wellness Ave, Health Town',
                'emergency_contact' => 'Tom Johnson',
                'blood_type' => 'O-',
                'medical_history' => 'Asthma, Migraine',
                'allergies' => 'Aspirin, Pollen'
            ],
            [
                'patient_id' => 'P1003',
                'first_name' => 'Michael',
                'last_name' => 'Brown',
                'date_of_birth' => '1975-11-30',
                'gender' => 'male',
                'email' => 'm.brown@email.com',
                'phone' => '555-0125',
                'address' => '789 Care Blvd, Medical City',
                'emergency_contact' => 'Lisa Brown',
                'blood_type' => 'B+',
                'medical_history' => 'Arthritis, High Cholesterol',
                'allergies' => 'Sulfa Drugs'
            ]
        ];

        // (Optional) Log demo patients' names and ages (or date of birth) so that they are clearly visible in the seeder.
        foreach ($patients as $patient) {
            $dob = Carbon::parse($patient['date_of_birth']);
            $age = $dob->age;
            \Log::info("Demo Patient: " . $patient['first_name'] . " " . $patient['last_name'] . " (Age: " . $age . ")");
        }

        // Create clinical guidelines
        $guidelines = [
            [
                'condition_name' => 'Hypertension',
                'condition_code' => 'I10',
                'treatment_options' => [
                    [
                        'name' => 'Lisinopril',
                        'dosage' => '10mg daily',
                        'contraindications' => ['Pregnancy', 'Angioedema']
                    ],
                    [
                        'name' => 'Amlodipine',
                        'dosage' => '5mg daily',
                        'contraindications' => ['Cardiogenic shock']
                    ]
                ],
                'alternative_treatments' => [
                    [
                        'name' => 'Lifestyle Modification',
                        'description' => 'Diet and exercise program',
                        'contraindications' => []
                    ]
                ]
            ],
            [
                'condition_name' => 'Type 2 Diabetes',
                'condition_code' => 'E11',
                'treatment_options' => [
                    [
                        'name' => 'Metformin',
                        'dosage' => '500mg twice daily',
                        'contraindications' => ['Renal failure']
                    ],
                    [
                        'name' => 'Insulin Glargine',
                        'dosage' => '10 units daily',
                        'contraindications' => ['Hypoglycemia']
                    ]
                ],
                'alternative_treatments' => [
                    [
                        'name' => 'Dietary Management',
                        'description' => 'Carbohydrate counting and meal planning',
                        'contraindications' => []
                    ]
                ]
            ]
        ];

        // Create patients and their medical records
        foreach ($patients as $patientData) {
            $patient = Patient::create($patientData);

            // Get a provider (user) for provider_id
            $provider = User::first();

            // Create 2-3 medical records for each patient
            $numberOfRecords = rand(2, 3);
            for ($i = 0; $i < $numberOfRecords; $i++) {
                $date = Carbon::now()->subMonths(rand(1, 12));
                $guideline = $guidelines[array_rand($guidelines)];

                MedicalRecord::create([
                    'patient_id' => $patient->id,
                    'provider_id' => $provider ? $provider->id : 1,
                    'visit_date' => $date->format('Y-m-d'),
                    'visit_type' => 'Routine',
                    'chief_complaint' => 'Follow-up visit',
                    'diagnosis' => $guideline['condition_name'],
                    'treatment_plan' => $guideline['treatment_options'][0]['name'] . ' - ' . $guideline['treatment_options'][0]['dosage'],
                    'prescription' => json_encode([
                        'medications' => [
                            [
                                'name' => $guideline['treatment_options'][0]['name'],
                                'dosage' => $guideline['treatment_options'][0]['dosage'],
                                'start_date' => $date->format('Y-m-d'),
                                'end_date' => $date->copy()->addMonths(3)->format('Y-m-d')
                            ]
                        ]
                    ]),
                    'notes' => 'Regular checkup and medication review. Patient responding well to treatment.',
                    'vitals' => json_encode([
                        'blood_pressure' => rand(110, 140) . '/' . rand(60, 90),
                        'heart_rate' => rand(60, 100),
                        'temperature' => number_format(rand(970, 990) / 10, 1),
                        'weight' => rand(60, 100)
                    ]),
                    'lab_results' => json_encode([]),
                    'status' => 'active',
                    'created_at' => $date,
                    'updated_at' => $date
                ]);
            }
        }

        // Create clinical guidelines
        foreach ($guidelines as $guideline) {
            ClinicalGuideline::create([
                'condition_name' => $guideline['condition_name'],
                'condition_code' => $guideline['condition_code'],
                'treatment_options' => json_encode($guideline['treatment_options']),
                'alternative_treatments' => json_encode($guideline['alternative_treatments']),
            ]);
        }
    }
}
