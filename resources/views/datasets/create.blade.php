<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upload New Dataset') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('datasets.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Dataset Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Dataset Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Source Name -->
                            <div>
                                <label for="source_name" class="block text-sm font-medium text-gray-700">Hospital/Facility Name</label>
                                <input type="text" name="source_name" id="source_name" value="{{ old('source_name') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('source_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Data Type -->
                            <div>
                                <label for="data_type" class="block text-sm font-medium text-gray-700">Data Type</label>
                                <select name="data_type" id="data_type" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select a data type</option>
                                    <option value="patient_records" {{ old('data_type') == 'patient_records' ? 'selected' : '' }}>Patient Records</option>
                                    <option value="billing" {{ old('data_type') == 'billing' ? 'selected' : '' }}>Billing Data</option>
                                    <option value="lab_results" {{ old('data_type') == 'lab_results' ? 'selected' : '' }}>Lab Results</option>
                                    <option value="pharmacy" {{ old('data_type') == 'pharmacy' ? 'selected' : '' }}>Pharmacy Data</option>
                                    <option value="imaging" {{ old('data_type') == 'imaging' ? 'selected' : '' }}>Imaging Data</option>
                                </select>
                                @error('data_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- File Format -->
                            <div>
                                <label for="format" class="block text-sm font-medium text-gray-700">File Format</label>
                                <select name="format" id="format" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select a format</option>
                                    <option value="csv" {{ old('format') == 'csv' ? 'selected' : '' }}>CSV</option>
                                    <option value="json" {{ old('format') == 'json' ? 'selected' : '' }}>JSON</option>
                                    <option value="xml" {{ old('format') == 'xml' ? 'selected' : '' }}>XML</option>
                                </select>
                                @error('format')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Date Range -->
                            <div>
                                <label for="data_period_start" class="block text-sm font-medium text-gray-700">Start Date</label>
                                <input type="date" name="data_period_start" id="data_period_start" value="{{ old('data_period_start') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('data_period_start')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="data_period_end" class="block text-sm font-medium text-gray-700">End Date</label>
                                <input type="date" name="data_period_end" id="data_period_end" value="{{ old('data_period_end') }}" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('data_period_end')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Dataset File -->
                            <div class="md:col-span-2">
                                <label for="dataset_file" class="block text-sm font-medium text-gray-700">Dataset File</label>
                                <input type="file" name="dataset_file" id="dataset_file" required
                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <p class="mt-1 text-sm text-gray-500">Maximum file size: 10MB. Supported formats: CSV, JSON, XML</p>
                                @error('dataset_file')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Additional Metadata -->
                            <div class="md:col-span-2">
                                <label for="metadata" class="block text-sm font-medium text-gray-700">Additional Metadata (JSON)</label>
                                <textarea name="metadata" id="metadata" rows="4"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder='{"department": "Emergency", "version": "1.0", "notes": "..."}'>{{ old('metadata') }}</textarea>
                                <p class="mt-1 text-sm text-gray-500">Optional: Add any additional metadata in JSON format</p>
                                @error('metadata')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('datasets.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </a>
                            <button type="submit" class="bg-indigo-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Upload Dataset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Validate date range
        document.getElementById('data_period_end').addEventListener('change', function() {
            const startDate = document.getElementById('data_period_start').value;
            const endDate = this.value;

            if (startDate && endDate && startDate > endDate) {
                alert('End date must be after start date');
                this.value = '';
            }
        });

        // Validate metadata JSON
        document.getElementById('metadata').addEventListener('change', function() {
            const value = this.value.trim();
            if (value) {
                try {
                    JSON.parse(value);
                } catch (e) {
                    alert('Invalid JSON format in metadata');
                    this.value = '';
                }
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const fileInput = document.getElementById('dataset_file');
            const formatSelect = document.getElementById('format');
            const previewButton = document.createElement('button');
            const previewResults = document.createElement('div');

            previewButton.type = 'button';
            previewButton.className = 'mt-2 bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700';
            previewButton.textContent = 'Preview Data';
            previewButton.style.display = 'none';

            previewResults.className = 'mt-4 p-4 bg-gray-50 rounded-lg';
            previewResults.style.display = 'none';

            fileInput.parentNode.appendChild(previewButton);
            fileInput.parentNode.appendChild(previewResults);

            fileInput.addEventListener('change', function() {
                previewButton.style.display = this.files.length ? 'inline-block' : 'none';
                previewResults.style.display = 'none';
            });

            previewButton.addEventListener('click', async function() {
                const formData = new FormData();
                formData.append('dataset_file', fileInput.files[0]);
                formData.append('format', formatSelect.value);

                try {
                    const response = await fetch('{{ route('datasets.preview') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    if (response.ok) {
                        let html = '<h3 class="font-medium text-lg mb-2">Data Preview</h3>';

                        if (data.errors && data.errors.length) {
                            html += '<div class="mb-4 p-3 bg-red-100 text-red-700 rounded">';
                            html += '<h4 class="font-medium">Validation Errors:</h4>';
                            html += '<ul class="list-disc list-inside">';
                            data.errors.forEach(error => {
                                html += `<li>${error}</li>`;
                            });
                            html += '</ul>';
                        }

                        previewResults.innerHTML = html;
                        previewResults.style.display = 'block';
                    }
                } catch (e) {
                    alert('Error previewing data: ' + e.message);
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
