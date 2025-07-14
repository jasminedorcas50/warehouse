<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Healthcare Data Warehouse</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <div id="app" class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-lg">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <h1 class="text-xl font-bold text-gray-800">Healthcare Data Warehouse</h1>
                        </div>
                        <div class="hidden md:ml-6 md:flex md:space-x-8">
                            <a href="/dashboard" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Dashboard
                            </a>
                            <a href="/patients" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Patients
                            </a>
                            <a href="/analytics" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Analytics
                            </a>
                            <a href="/reports" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-bold mb-4">Reports</h1>

            <!-- Report Generation Form -->
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-lg font-medium mb-4">Generate Report</h2>
                <form @submit.prevent="generateReport">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="report_type" class="block text-sm font-medium text-gray-700">Report Type</label>
                            <select v-model="reportForm.report_type" id="report_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="patient_demographics">Patient Demographics</option>
                                <option value="medical_records_summary">Medical Records Summary</option>
                                <option value="common_diagnoses">Common Diagnoses</option>
                                <option value="treatment_outcomes">Treatment Outcomes</option>
                            </select>
                        </div>
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" v-model="reportForm.start_date" id="start_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" v-model="reportForm.end_date" id="end_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="mt-4 text-right">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Generate Report
                        </button>
                    </div>
                </form>
            </div>

            <!-- Report Display Area -->
            <div v-if="reportData.title" class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-bold mb-2">@{{ reportData.title }}</h2>
                <p class="text-gray-600 mb-4">@{{ reportData.description }}</p>

                <!-- Patient Demographics Report -->
                <div v-if="reportData.reportType === 'patient_demographics'">
                    <h3 class="text-lg font-medium mb-2">Gender Distribution</h3>
                    <canvas id="genderChart"></canvas>

                    <h3 class="text-lg font-medium mt-6 mb-2">Age Distribution</h3>
                    <canvas id="ageChart"></canvas>

                    <div class="mt-6">
                        <p class="text-md font-medium">Total Patients: <span class="font-bold">@{{ reportData.data.totalPatients }}</span></p>
                    </div>
                </div>

                <!-- Medical Records Summary Report -->
                <div v-if="reportData.reportType === 'medical_records_summary'">
                    <h3 class="text-lg font-medium mb-2">Records by Month</h3>
                    <canvas id="recordsByMonthChart"></canvas>

                    <h3 class="text-lg font-medium mt-6 mb-2">Visit Types</h3>
                    <canvas id="visitTypesChart"></canvas>

                    <div class="mt-6">
                        <p class="text-md font-medium">Total Medical Records: <span class="font-bold">@{{ reportData.data.totalRecords }}</span></p>
                    </div>
                </div>

                <!-- Common Diagnoses Report -->
                <div v-if="reportData.reportType === 'common_diagnoses'">
                    <h3 class="text-lg font-medium mb-2">Top 10 Common Diagnoses</h3>
                    <table class="min-w-full divide-y divide-gray-200 mt-4">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diagnosis</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="item in reportData.data.commonDiagnoses" :key="item.diagnosis">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">@{{ item.diagnosis }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">@{{ item.count }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="mt-4">
                        <p class="text-md font-medium">Total Diagnoses Count (in top 10): <span class="font-bold">@{{ reportData.data.totalDiagnosesCount }}</span></p>
                    </div>
                </div>

                <!-- Treatment Outcomes Report -->
                <div v-if="reportData.reportType === 'treatment_outcomes'">
                    <h3 class="text-lg font-medium mb-2">Treatment Outcomes Summary</h3>
                    <canvas id="outcomesChart"></canvas>

                    <div class="mt-6">
                        <p class="text-md font-medium">Total Treatments Considered: <span class="font-bold">@{{ reportData.data.totalTreatmentsConsidered }}</span></p>
                    </div>
                </div>

                <p v-if="!reportData.title && !loading" class="text-gray-600">Select a report type and date range to generate a report.</p>
                <p v-if="loading" class="text-gray-600">Generating report...</p>
            </div>
        </main>
    </div>

    <script>
        new Vue({
            el: '#app',
            data: {
                reportForm: {
                    report_type: 'patient_demographics',
                    start_date: new Date(new Date().setFullYear(new Date().getFullYear() - 1)).toISOString().split('T')[0],
                    end_date: new Date().toISOString().split('T')[0],
                },
                reportData: {},
                loading: false,
                charts: {
                    genderChart: null,
                    ageChart: null,
                    recordsByMonthChart: null,
                    visitTypesChart: null,
                    outcomesChart: null,
                }
            },
            mounted() {
                this.generateReport(); // Generate a default report on page load
            },
            methods: {
                async generateReport() {
                    this.loading = true;
                    this.reportData = {}; // Clear previous report data
                    this.destroyCharts(); // Destroy existing chart instances

                    try {
                        const response = await axios.get('/api/reports/generate', {
                            params: this.reportForm
                        });
                        this.reportData = response.data;
                        this.$nextTick(() => {
                            this.renderCharts();
                        });
                    } catch (error) {
                        console.error('Error generating report:', error);
                        alert('Error generating report. Please try again.');
                    } finally {
                        this.loading = false;
                    }
                },
                destroyCharts() {
                    for (const chartName in this.charts) {
                        if (this.charts[chartName]) {
                            this.charts[chartName].destroy();
                            this.charts[chartName] = null;
                        }
                    }
                },
                renderCharts() {
                    if (this.reportData.reportType === 'patient_demographics') {
                        const genderCtx = document.getElementById('genderChart').getContext('2d');
                        this.charts.genderChart = new Chart(genderCtx, {
                            type: 'pie',
                            data: {
                                labels: this.reportData.data.genderDistribution.map(item => item.gender),
                                datasets: [{
                                    data: this.reportData.data.genderDistribution.map(item => item.count),
                                    backgroundColor: ['#4F46E5', '#EC4899', '#6B7280']
                                }]
                            },
                            options: { responsive: true }
                        });

                        const ageCtx = document.getElementById('ageChart').getContext('2d');
                        this.charts.ageChart = new Chart(ageCtx, {
                            type: 'bar',
                            data: {
                                labels: this.reportData.data.ageDistribution.map(item => item.age_group),
                                datasets: [{
                                    label: 'Number of Patients',
                                    data: this.reportData.data.ageDistribution.map(item => item.count),
                                    backgroundColor: '#10B981'
                                }]
                            },
                            options: {
                                responsive: true,
                                scales: { y: { beginAtZero: true } }
                            }
                        });
                    } else if (this.reportData.reportType === 'medical_records_summary') {
                        const recordsByMonthCtx = document.getElementById('recordsByMonthChart').getContext('2d');
                        this.charts.recordsByMonthChart = new Chart(recordsByMonthCtx, {
                            type: 'line',
                            data: {
                                labels: this.reportData.data.recordsByMonth.map(item => item.month),
                                datasets: [{
                                    label: 'Medical Records',
                                    data: this.reportData.data.recordsByMonth.map(item => item.count),
                                    borderColor: '#4F46E5',
                                    tension: 0.1
                                }]
                            },
                            options: { responsive: true, scales: { y: { beginAtZero: true } } }
                        });

                        const visitTypesCtx = document.getElementById('visitTypesChart').getContext('2d');
                        this.charts.visitTypesChart = new Chart(visitTypesCtx, {
                            type: 'pie',
                            data: {
                                labels: this.reportData.data.visitTypes.map(item => item.visit_type),
                                datasets: [{
                                    data: this.reportData.data.visitTypes.map(item => item.count),
                                    backgroundColor: ['#F59E0B', '#EC4899', '#3B82F6', '#10B981', '#7C3AED']
                                }]
                            },
                            options: { responsive: true }
                        });
                    } else if (this.reportData.reportType === 'treatment_outcomes') {
                        const outcomesCtx = document.getElementById('outcomesChart').getContext('2d');
                        this.charts.outcomesChart = new Chart(outcomesCtx, {
                            type: 'doughnut',
                            data: {
                                labels: this.reportData.data.outcomesSummary.map(item => item.outcome),
                                datasets: [{
                                    data: this.reportData.data.outcomesSummary.map(item => item.count),
                                    backgroundColor: ['#10B981', '#4F46E5', '#EF4444', '#6B7280']
                                }]
                            },
                            options: { responsive: true }
                        });
                    }
                }
            }
        });
    </script>
</body>
</html>
