<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $dataset->name }}
            </h2>
            <div class="flex space-x-3">
                @if($dataset->status === 'imported')
                    <a href="{{ route('datasets.download', $dataset) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download Dataset
                    </a>
                @endif
                <form action="{{ route('datasets.destroy', $dataset) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Are you sure you want to delete this dataset?')" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete Dataset
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Dataset Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Basic Information</h3>
                            <dl class="mt-4 space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Source</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $dataset->source_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Data Type</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ ucwords(str_replace('_', ' ', $dataset->data_type)) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Format</dt>
                                    <dd class="mt-1 text-sm text-gray-900 uppercase">{{ $dataset->format }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Time Period</h3>
                            <dl class="mt-4 space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Start Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $dataset->data_period_start ? $dataset->data_period_start->format('M d, Y') : 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">End Date</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $dataset->data_period_end ? $dataset->data_period_end->format('M d, Y') : 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Uploaded</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $dataset->created_at ? $dataset->created_at->format('M d, Y H:i:s') : 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Status</h3>
                            <dl class="mt-4 space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($dataset->status === 'imported') bg-green-100 text-green-800
                                            @elseif($dataset->status === 'processing') bg-yellow-100 text-yellow-800
                                            @elseif($dataset->status === 'failed') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($dataset->status) }}
                                        </span>
                                    </dd>
                                </div>
                                @if($dataset->status === 'imported')
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Records Imported</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ number_format($dataset->records_imported) }}</dd>
                                    </div>
                                @endif
                                @if($dataset->status === 'failed')
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Last Error</dt>
                                        <dd class="mt-1 text-sm text-red-600">{{ $dataset->last_error }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    @if($dataset->metadata)
                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900">Additional Metadata</h3>
                            <div class="mt-4 bg-gray-50 rounded-lg p-4">
                                <pre class="text-sm text-gray-900 overflow-x-auto">{{ json_encode(json_decode($dataset->metadata), JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Import Logs -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Import History</h3>

                    @if($dataset->importLogs->isEmpty())
                        <p class="text-gray-500 text-sm">No import history available.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Started At</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed At</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Records Processed</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($dataset->importLogs as $log)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $log->started_at ? $log->started_at->format('M d, Y H:i:s') : 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $log->completed_at ? $log->completed_at->format('M d, Y H:i:s') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                    @if($log->status === 'completed') bg-green-100 text-green-800
                                                    @elseif($log->status === 'processing') bg-yellow-100 text-yellow-800
                                                    @elseif($log->status === 'failed') bg-red-100 text-red-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ ucfirst($log->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ number_format($log->records_processed) }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                @if($log->error_message)
                                                    <span class="text-red-600">{{ $log->error_message }}</span>
                                                @else
                                                    {{ $log->message ?: '-' }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if($dataset->status === 'failed')
                        <div class="mt-6">
                            <form action="{{ route('datasets.import', $dataset) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Retry Import
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
