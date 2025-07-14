<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg card-custom">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Patient Analytics</h3>
                        <p class="text-gray-600">View detailed patient analytics and trends</p>
                        <a href="{{ route('analytics') }}" class="mt-4 inline-block btn btn-custom btn-primary-custom">View Analytics</a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg card-custom">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Patient Management</h3>
                        <p class="text-gray-600">Manage patient records and medical history</p>
                        <a href="{{ route('patients') }}" class="mt-4 inline-block btn btn-custom btn-primary-custom">View Patients</a>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg card-custom">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Reports</h3>
                        <p class="text-gray-600">Generate and view detailed reports</p>
                        <a href="{{ route('reports') }}" class="mt-4 inline-block btn btn-custom btn-primary-custom">View Reports</a>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg card-custom">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                    <div class="space-y-4">
                        <p class="text-gray-600">No recent activity to display.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
