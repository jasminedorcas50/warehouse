<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\MedicalRecord;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function generateReport(Request $request)
    {
        $reportType = $request->input('report_type');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        try {
            $start = $startDate ? Carbon::parse($startDate) : Carbon::now()->subYear();
            $end = $endDate ? Carbon::parse($endDate) : Carbon::now();
        } catch (\Exception $e) {
            $start = Carbon::now()->subYear();
            $end = Carbon::now();
        }

        $data = [];
        $reportTitle = "";
        $reportDescription = "";

        switch ($reportType) {
            case 'patient_demographics':
                $data = $this->getPatientDemographicsReport();
                $reportTitle = "Patient Demographics Report";
                $reportDescription = "Overview of patient distribution by gender and age groups.";
                break;
            case 'medical_records_summary':
                $data = $this->getMedicalRecordsSummaryReport($start, $end);
                $reportTitle = "Medical Records Summary Report";
                $reportDescription = "Summary of medical records within the selected date range.";
                break;
            case 'common_diagnoses':
                $data = $this->getCommonDiagnosesReport($start, $end);
                $reportTitle = "Common Diagnoses Report";
                $reportDescription = "Top 10 most common diagnoses within the selected date range.";
                break;
            case 'treatment_outcomes':
                $data = $this->getTreatmentOutcomesReport($start, $end);
                $reportTitle = "Treatment Outcomes Report";
                $reportDescription = "Analysis of treatment outcomes for medical records within the selected date range.";
                break;
            default:
                $data = $this->getPatientDemographicsReport();
                $reportTitle = "Patient Demographics Report";
                $reportDescription = "Overview of patient distribution by gender and age groups.";
                break;
        }

        return response()->json([
            'title' => $reportTitle,
            'description' => $reportDescription,
            'data' => $data,
            'reportType' => $reportType
        ]);
    }

    private function getPatientDemographicsReport()
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
            'genderDistribution' => $genderDistribution,
            'ageDistribution' => $ageDistribution,
            'totalPatients' => Patient::count()
        ];
    }

    private function getMedicalRecordsSummaryReport(Carbon $start, Carbon $end)
    {
        $totalRecords = MedicalRecord::whereBetween('visit_date', [$start, $end])->count();
        $recordsByMonth = MedicalRecord::whereBetween('visit_date', [$start, $end])
            ->selectRaw('DATE_FORMAT(visit_date, "%Y-%m") as month, count(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $visitTypes = MedicalRecord::whereBetween('visit_date', [$start, $end])
            ->selectRaw('visit_type, count(*) as count')
            ->groupBy('visit_type')
            ->orderByDesc('count')
            ->get();

        return [
            'totalRecords' => $totalRecords,
            'recordsByMonth' => $recordsByMonth,
            'visitTypes' => $visitTypes
        ];
    }

    private function getCommonDiagnosesReport(Carbon $start, Carbon $end)
    {
        $commonDiagnoses = MedicalRecord::whereBetween('created_at', [$start, $end])
            ->selectRaw('diagnosis, count(*) as count')
            ->groupBy('diagnosis')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return [
            'commonDiagnoses' => $commonDiagnoses,
            'totalDiagnosesCount' => $commonDiagnoses->sum('count')
        ];
    }

    private function getTreatmentOutcomesReport(Carbon $start, Carbon $end)
    {
        // This is a placeholder. Real implementation would involve more complex logic
        // based on how outcomes are tracked (e.g., specific fields in medical records
        // or a dedicated outcomes table).

        $outcomesSummary = MedicalRecord::whereBetween('visit_date', [$start, $end])
            ->selectRaw('CASE
                            WHEN notes LIKE "%improved%" THEN "Improved"
                            WHEN notes LIKE "%resolved%" THEN "Resolved"
                            WHEN notes LIKE "%no change%" THEN "No Change"
                            ELSE "Unknown"
                        END as outcome,
                        count(*) as count')
            ->groupBy('outcome')
            ->get();

        return [
            'outcomesSummary' => $outcomesSummary,
            'totalTreatmentsConsidered' => $outcomesSummary->sum('count')
        ];
    }
}
