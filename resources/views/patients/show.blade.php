@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-2xl font-bold mb-4">Patient Details</h2>
        <div class="grid grid-cols-2 gap-4">
            <div><strong>Patient ID:</strong> {{ $patient->patient_id }}</div>
            <div><strong>Age:</strong> {{ $patient->age }}</div>
            <div><strong>Gender:</strong> {{ ucfirst($patient->gender) }}</div>
            <div><strong>Condition:</strong> {{ $patient->condition }}</div>
            <div><strong>Procedure:</strong> {{ $patient->procedure }}</div>
            <div><strong>Cost:</strong> ${{ number_format($patient->cost, 2) }}</div>
            <div><strong>Length of Stay:</strong> {{ $patient->length_of_stay }} days</div>
            <div><strong>Readmission:</strong> {{ $patient->readmission ? 'Yes' : 'No' }}</div>
            <div><strong>Outcome:</strong> {{ $patient->outcome }}</div>
            <div><strong>Satisfaction:</strong> {{ $patient->satisfaction }}/5</div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-xl font-semibold mb-4">Medical Records</h3>
        @if($patient->medicalRecords->count())
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Visit Date</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Diagnosis</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Treatment Plan</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($patient->medicalRecords as $record)
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $record->visit_date ? $record->visit_date->format('Y-m-d') : '' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $record->diagnosis }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $record->treatment_plan }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $record->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-gray-600">No medical records found for this patient.</p>
        @endif
    </div>
    <div class="mt-6">
        <a href="{{ route('patients') }}" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Back to Patients</a>
    </div>
</div>
@endsection
