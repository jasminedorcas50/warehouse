<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index()
    {
        // "view analytics" – returns a Blade view (analytics.index) with analytics data.
        return view('analytics.index', ['data' => $this->getAnalyticsData()]);
    }

    public function patientAnalytics(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $metricType = $request->input('metric_type', 'visits'); // Default to visits

        // Default date range if not provided or invalid
        try {
            $start = $startDate ? Carbon::parse($startDate) : Carbon::now()->subMonths(6);
            $end = $endDate ? Carbon::parse($endDate) : Carbon::now();
        } catch (\Exception $e) {
            $start = Carbon::now()->subMonths(6);
            $end = Carbon::now();
        }

        $data = [];

        switch ($metricType) {
            case 'visits':
                $data = $this->getVisitAnalytics($start, $end);
                break;
            case 'diagnoses':
                $data = $this->getDiagnosisAnalytics($start, $end);
                break;
            case 'treatments':
                $data = $this->getTreatmentAnalytics($start, $end);
                break;
            case 'demographics':
                $data = $this->getDemographicAnalytics();
                break;
            default:
                $data = $this->getVisitAnalytics($start, $end); // Default
                break;
        }

        return response()->json($data);
    }

    private function getAnalyticsData()
    {
        // (Old "analytics" logic, now renamed and made private.)
        $totalPatients = Patient::count();
        $totalRecords = MedicalRecord::count();
        $avgVitals = MedicalRecord::selectRaw('avg(json_extract(vitals, "$.heart_rate")) as avg_heart_rate, avg(json_extract(vitals, "$.weight")) as avg_weight')->first();
        $avgHeartRate = (int)($avgVitals->avg_heart_rate ?? 0);
        $avgWeight = (int)($avgVitals->avg_weight ?? 0);
        $commonDiagnoses = MedicalRecord::selectRaw('diagnosis, count(*) as count')->groupBy('diagnosis')->orderByDesc('count')->limit(5)->get();

        return [
            'totalPatients' => $totalPatients,
            'totalRecords' => $totalRecords,
            'avgHeartRate' => $avgHeartRate,
            'avgWeight' => $avgWeight,
            'commonDiagnoses' => $commonDiagnoses
        ];
    }

    private function getVisitAnalytics(Carbon $start, Carbon $end)
    {
        $visitsTrend = MedicalRecord::whereBetween('visit_date', [$start, $end])
            ->selectRaw('DATE(visit_date) as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $visitTypeDistribution = MedicalRecord::whereBetween('visit_date', [$start, $end])
            ->selectRaw('visit_type, count(*) as count')
            ->groupBy('visit_type')
            ->orderByDesc('count')
            ->get();

        return [
            'trendData' => $visitsTrend->map(function ($item) {
                return ['date' => $item->date, 'count' => $item->count];
            }),
            'distributionData' => $visitTypeDistribution->map(function ($item) {
                return ['label' => $item->visit_type, 'value' => $item->count];
            }),
            'keyMetrics' => [
                ['name' => 'Total Visits', 'value' => $visitsTrend->sum('count')],
                ['name' => 'Avg Visits per Day', 'value' => round($visitsTrend->avg('count'), 2) ?? 0]
            ],
            'tableHeaders' => ['Date', 'Visit Count', 'Visit Type', 'Type Count'],
            'tableData' => $visitsTrend->crossJoin($visitTypeDistribution)->map(function ($item) {
                return [
                    'date' => $item[0]->date,
                    'visit_count' => $item[0]->count,
                    'visit_type' => $item[1]->visit_type,
                    'type_count' => $item[1]->count
                ];
            })
        ];
    }

    private function getDiagnosisAnalytics(Carbon $start, Carbon $end)
    {
        $diagnosisTrend = MedicalRecord::whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $commonDiagnoses = MedicalRecord::whereBetween('created_at', [$start, $end])
            ->selectRaw('diagnosis, count(*) as count')
            ->groupBy('diagnosis')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return [
            'trendData' => $diagnosisTrend->map(function ($item) {
                return ['date' => $item->date, 'count' => $item->count];
            }),
            'distributionData' => $commonDiagnoses->map(function ($item) {
                return ['label' => $item->diagnosis, 'value' => $item->count];
            }),
            'keyMetrics' => [
                ['name' => 'Total Diagnoses', 'value' => $diagnosisTrend->sum('count')],
                ['name' => 'Most Common Diagnosis', 'value' => $commonDiagnoses->first()->diagnosis ?? 'N/A']
            ],
            'tableHeaders' => ['Date', 'Diagnosis Count', 'Diagnosis', 'Frequency'],
            'tableData' => $diagnosisTrend->crossJoin($commonDiagnoses)->map(function ($item) {
                return [
                    'date' => $item[0]->date,
                    'diagnosis_count' => $item[0]->count,
                    'diagnosis' => $item[1]->diagnosis,
                    'frequency' => $item[1]->count
                ];
            })
        ];
    }

    private function getTreatmentAnalytics(Carbon $start, Carbon $end)
    {
        $treatmentTrend = MedicalRecord::whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $commonTreatments = MedicalRecord::whereBetween('created_at', [$start, $end])
            ->selectRaw('treatment_plan, count(*) as count')
            ->groupBy('treatment_plan')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return [
            'trendData' => $treatmentTrend->map(function ($item) {
                return ['date' => $item->date, 'count' => $item->count];
            }),
            'distributionData' => $commonTreatments->map(function ($item) {
                return ['label' => $item->treatment_plan, 'value' => $item->count];
            }),
            'keyMetrics' => [
                ['name' => 'Total Treatments', 'value' => $treatmentTrend->sum('count')],
                ['name' => 'Most Common Treatment', 'value' => $commonTreatments->first()->treatment_plan ?? 'N/A']
            ],
            'tableHeaders' => ['Date', 'Treatment Count', 'Treatment', 'Frequency'],
            'tableData' => $treatmentTrend->crossJoin($commonTreatments)->map(function ($item) {
                return [
                    'date' => $item[0]->date,
                    'treatment_count' => $item[0]->count,
                    'treatment' => $item[1]->treatment_plan,
                    'frequency' => $item[1]->count
                ];
            })
        ];
    }

    private function getDemographicAnalytics()
    {
        $genderDistribution = Patient::selectRaw('gender, count(*) as count')
            ->groupBy('gender')
            ->get();

        $ageDistribution = Patient::selectRaw('
            CASE
                WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 18 THEN "Under 18"
                WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 18 AND 30 THEN "18-30"
                WHEN TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 31 AND 50 THEN "31-50"
                ELSE "Over 50"
            END as age_group,
            count(*) as count
        ')
            ->groupBy('age_group')
            ->orderBy('age_group')
            ->get();

        return [
            'trendData' => [], // Not applicable for demographics trend in this context
            'distributionData' => $genderDistribution->map(function ($item) {
                return ['label' => ucfirst($item->gender), 'value' => $item->count];
            }),
            'keyMetrics' => [
                ['name' => 'Total Patients', 'value' => Patient::count()],
                ['name' => 'Average Patient Age', 'value' => round(Patient::selectRaw('AVG(TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE())) as avg_age')->first()->avg_age ?? 0)]
            ],
            'tableHeaders' => ['Gender', 'Count', 'Age Group', 'Count'],
            'tableData' => $genderDistribution->crossJoin($ageDistribution)->map(function ($item) {
                return [
                    'gender' => ucfirst($item[0]->gender),
                    'gender_count' => $item[0]->count,
                    'age_group' => $item[1]->age_group,
                    'age_group_count' => $item[1]->count
                ];
            })
        ];
    }
}
