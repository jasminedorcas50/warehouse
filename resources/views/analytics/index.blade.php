<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Analytics - Healthcare Data Warehouse</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
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
                            <a href="/analytics" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Analytics
                            </a>
                            <a href="/reports" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Date Range Filter -->
            <div class="mb-6 bg-white shadow rounded-lg p-4">
                <div class="flex items-center space-x-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" v-model="startDate" @change="fetchAnalytics" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="date" v-model="endDate" @change="fetchAnalytics" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Metric Type</label>
                        <select v-model="selectedMetric" @change="fetchAnalytics" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="visits">Patient Visits</option>
                            <option value="diagnoses">Diagnoses</option>
                            <option value="treatments">Treatments</option>
                            <option value="demographics">Demographics</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Analytics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Trend Analysis -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Trend Analysis</h3>
                    <canvas id="trendChart"></canvas>
                </div>

                <!-- Distribution Analysis -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Distribution Analysis</h3>
                    <canvas id="distributionChart"></canvas>
                </div>

                <!-- Predictive Analytics -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Predictive Analytics</h3>
                    <canvas id="predictionChart"></canvas>
                </div>

                <!-- Key Metrics -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Key Metrics</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div v-for="metric in keyMetrics" :key="metric.name" class="bg-gray-50 p-4 rounded-lg">
                            <dt class="text-sm font-medium text-gray-500">@{{ metric.name }}</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900">@{{ metric.value }}</dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Analysis Table -->
            <div class="mt-6 bg-white shadow rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Detailed Analysis</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th v-for="header in tableHeaders" :key="header" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        @{{ header }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="row in tableData" :key="row.id">
                                    <td v-for="header in tableHeaders" :key="header" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @{{ row[header.toLowerCase()] }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        new Vue({
            el: '#app',
            data: {
                startDate: new Date(new Date().setMonth(new Date().getMonth() - 6)).toISOString().split('T')[0],
                endDate: new Date().toISOString().split('T')[0],
                selectedMetric: 'visits',
                keyMetrics: [],
                tableHeaders: [],
                tableData: [],
                charts: {
                    trend: null,
                    distribution: null,
                    prediction: null
                }
            },
            mounted() {
                this.initializeCharts();
                this.fetchAnalytics();
            },
            methods: {
                async fetchAnalytics() {
                    try {
                        const response = await axios.get('/api/analytics/patient-analytics', {
                            params: {
                                start_date: this.startDate,
                                end_date: this.endDate,
                                metric_type: this.selectedMetric
                            }
                        });

                        // Clear previous data
                        this.keyMetrics = [];
                        this.tableHeaders = [];
                        this.tableData = [];

                        this.updateCharts(response.data);
                        this.updateTable(response.data);
                        this.updateMetrics(response.data);
                    } catch (error) {
                        console.error('Error fetching analytics:', error);
                    }
                },
                initializeCharts() {
                    // Initialize trend chart
                    const trendCtx = document.getElementById('trendChart').getContext('2d');
                    this.charts.trend = new Chart(trendCtx, {
                        type: 'line',
                        data: {
                            labels: [],
                            datasets: [{
                                label: 'Trend',
                                data: [],
                                borderColor: '#4F46E5',
                                tension: 0.1,
                                fill: false
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                legend: { display: true }
                            }
                        }
                    });

                    // Initialize distribution chart
                    const distributionCtx = document.getElementById('distributionChart').getContext('2d');
                    this.charts.distribution = new Chart(distributionCtx, {
                        type: 'pie',
                        data: {
                            labels: [],
                            datasets: [{
                                data: [],
                                backgroundColor: [
                                    '#4F46E5',
                                    '#7C3AED',
                                    '#EC4899',
                                    '#F59E0B',
                                    '#10B981',
                                    '#EF4444',
                                    '#3B82F6',
                                    '#6B7280'
                                ],
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { position: 'top' }
                            }
                        }
                    });

                    // Initialize prediction chart (placeholder for now)
                    const predictionCtx = document.getElementById('predictionChart').getContext('2d');
                    this.charts.prediction = new Chart(predictionCtx, {
                        type: 'bar',
                        data: {
                            labels: [],
                            datasets: [{
                                label: 'Prediction',
                                data: [],
                                backgroundColor: '#10B981'
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                legend: { display: true }
                            }
                        }
                    });
                },
                updateCharts(data) {
                    // Update Trend Chart
                    if (this.charts.trend) {
                        this.charts.trend.data.labels = data.trendData.map(item => item.date);
                        this.charts.trend.data.datasets[0].data = data.trendData.map(item => item.count);
                        this.charts.trend.update();
                    }

                    // Update Distribution Chart
                    if (this.charts.distribution) {
                        this.charts.distribution.data.labels = data.distributionData.map(item => item.label);
                        this.charts.distribution.data.datasets[0].data = data.distributionData.map(item => item.value);
                        this.charts.distribution.update();
                    }

                    // Update Prediction Chart (placeholder for now, can be expanded later)
                    if (this.charts.prediction) {
                        // For now, let's use some dummy data or just display a message
                        // If predictive analytics are implemented, this would fetch that data.
                        this.charts.prediction.data.labels = ['Future Period 1', 'Future Period 2', 'Future Period 3'];
                        this.charts.prediction.data.datasets[0].data = [100, 120, 110]; // Dummy data
                        this.charts.prediction.update();
                    }
                },
                updateTable(data) {
                    this.tableHeaders = data.tableHeaders;
                    this.tableData = data.tableData;
                },
                updateMetrics(data) {
                    this.keyMetrics = data.keyMetrics;
                }
            }
        });
    </script>
</body>
</html>
