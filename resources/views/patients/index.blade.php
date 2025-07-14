<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Patient Management - Healthcare Data Warehouse</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Configure axios defaults
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        axios.defaults.withCredentials = true;

        // Add response interceptor for debugging
        axios.interceptors.response.use(
            response => response,
            error => {
                console.error('API Error:', {
                    status: error.response?.status,
                    data: error.response?.data,
                    config: error.config
                });
                return Promise.reject(error);
            }
        );
    </script>
</head>
<body class="bg-gray-100">
    <div id="app" class="min-h-screen">
        <!-- Navigation (same as dashboard) -->
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
                            <a href="/patients" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Patients
                            </a>
                            <a href="/analytics" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
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
            <!-- Search and Add Patient -->
            <div class="mb-6 flex justify-between items-center">
                <div class="flex-1 max-w-lg flex items-center">
                    <input
                        type="text"
                        v-model="searchQuery"
                        @keyup.enter="searchPatients"
                        placeholder="Search patients..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                    <button
                        @click="searchPatients"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-r-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                        Search
                    </button>
                </div>
                <button
                    @click="showAddPatientModal = true"
                    class="ml-4 px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                    Add Patient
                </button>
            </div>

            <!-- Patients Table -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <!-- Add a container with horizontal scroll -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Condition</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Procedure</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Length of Stay</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Readmission</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Outcome</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satisfaction</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="patient in patients" :key="patient.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">@{{ patient.patient_id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">@{{ patient.age }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">@{{ patient.gender }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">@{{ patient.condition }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">@{{ patient.procedure }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">@{{ patient.cost }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">@{{ patient.length_of_stay }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">@{{ patient.readmission ? 'Yes' : 'No' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">@{{ patient.outcome }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">@{{ patient.satisfaction }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button @click="viewPatient(patient)" class="text-indigo-600 hover:text-indigo-900 mr-3">View</button>
                                    <button @click="editPatient(patient)" class="text-yellow-600 hover:text-yellow-900 mr-3">Edit</button>
                                    <button @click="deletePatient(patient)" class="text-red-600 hover:text-red-900">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Add/Edit Patient Modal -->
            <div v-if="showAddPatientModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
                <div class="bg-white rounded-lg p-6 max-w-2xl w-full">
                    <h2 class="text-lg font-medium mb-4">@{{ editingPatient ? 'Edit Patient' : 'Add New Patient' }}</h2>
                    <form @submit.prevent="savePatient">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Age</label>
                                <input type="number" v-model="patientForm.age" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Gender</label>
                                <select v-model="patientForm.gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Condition</label>
                                <input type="text" v-model="patientForm.condition" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Procedure</label>
                                <input type="text" v-model="patientForm.procedure" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Cost</label>
                                <input type="number" step="0.01" v-model="patientForm.cost" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Length of Stay (days)</label>
                                <input type="number" v-model="patientForm.length_of_stay" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Readmission</label>
                                <select v-model="patientForm.readmission" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Outcome</label>
                                <input type="text" v-model="patientForm.outcome" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Satisfaction (1-5)</label>
                                <input type="number" min="1" max="5" v-model="patientForm.satisfaction" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end space-x-3">
                            <button type="button" @click="showAddPatientModal = false" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Save</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Patient Details Modal -->
            <div v-if="showPatientModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 max-w-2xl w-full overflow-y-auto max-h-[90vh]">
                    <h2 class="text-lg font-medium mb-4">Patient Details</h2>
                    <div v-if="selectedPatient" class="grid grid-cols-2 gap-4">
                        <div><strong>Patient ID:</strong> @{{ selectedPatient.patient_id }}</div>
                        <div><strong>Age:</strong> @{{ selectedPatient.age }}</div>
                        <div><strong>Gender:</strong> @{{ selectedPatient.gender }}</div>
                        <div><strong>Condition:</strong> @{{ selectedPatient.condition }}</div>
                        <div><strong>Procedure:</strong> @{{ selectedPatient.procedure }}</div>
                        <div><strong>Cost:</strong> $@{{ selectedPatient.cost }}</div>
                        <div><strong>Length of Stay:</strong> @{{ selectedPatient.length_of_stay }} days</div>
                        <div><strong>Readmission:</strong> @{{ selectedPatient.readmission ? 'Yes' : 'No' }}</div>
                        <div><strong>Outcome:</strong> @{{ selectedPatient.outcome }}</div>
                        <div><strong>Satisfaction:</strong> @{{ selectedPatient.satisfaction }}/5</div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button @click="closePatientModal" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Close</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        new Vue({
            el: '#app',
            data: {
                patients: @json($patients),
                searchQuery: '',
                showAddPatientModal: false,
                editingPatient: null,
                patientForm: {
                    age: null,
                    gender: '',
                    condition: '',
                    procedure: '',
                    cost: null,
                    length_of_stay: null,
                    readmission: false,
                    outcome: '',
                    satisfaction: null
                },
                selectedPatient: null,
                showPatientModal: false
            },
            mounted() {
                // Configure axios interceptors for error handling
                axios.interceptors.response.use(
                    response => response,
                    error => {
                        if (error.response && error.response.status === 419) {
                            // CSRF token mismatch
                            alert('Session expired. Please refresh the page and try again.');
                            window.location.reload();
                        }
                        return Promise.reject(error);
                    }
                );
            },
            methods: {
                async fetchPatients() {
                    try {
                        const response = await axios.get('/api/patients');
                        this.patients = response.data;
                    } catch (error) {
                        console.error('Error fetching patients:', error);
                    }
                },
                async searchPatients() {
                    if (this.searchQuery.length < 2) {
                        await this.fetchPatients();
                        return;
                    }
                    try {
                        const response = await axios.get(`/api/patients/search?q=${this.searchQuery}`);
                        this.patients = response.data;
                    } catch (error) {
                        console.error('Error searching patients:', error);
                    }
                },
                async viewPatient(patient) {
                    try {
                        const response = await axios.get(`/patients/${patient.id}`);
                        this.selectedPatient = response.data;
                        this.showPatientModal = true;
                    } catch (error) {
                        console.error('Error fetching patient details:', error);
                        alert('Error loading patient details. Please try again.');
                    }
                },
                editPatient(patient) {
                    this.editingPatient = patient;
                    // Create a copy of the patient data
                    this.patientForm = {
                        age: patient.age,
                        gender: patient.gender,
                        condition: patient.condition,
                        procedure: patient.procedure,
                        cost: patient.cost,
                        length_of_stay: patient.length_of_stay,
                        readmission: patient.readmission,
                        outcome: patient.outcome,
                        satisfaction: patient.satisfaction
                    };
                    this.showAddPatientModal = true;
                },
                async deletePatient(patient) {
                    if (!confirm('Are you sure you want to delete this patient?')) return;
                    try {
                        await axios.delete(`/api/patients/${patient.id}`);
                        this.patients = this.patients.filter(p => p.id !== patient.id);
                    } catch (error) {
                        console.error('Error deleting patient:', error);
                    }
                },
                async savePatient() {
                    try {
                        // Validate required fields
                        const requiredFields = ['age', 'gender', 'condition', 'procedure', 'cost', 'length_of_stay', 'readmission', 'outcome', 'satisfaction'];
                        const missingFields = requiredFields.filter(field => !this.patientForm[field]);

                        if (missingFields.length > 0) {
                            alert('Please fill in all required fields: ' + missingFields.join(', '));
                            return;
                        }

                        // Prepare the form data
                        const formData = { ...this.patientForm };

                        // Ensure date_of_birth is in YYYY-MM-DD format
                        if (formData.date_of_birth) {
                            formData.date_of_birth = new Date(formData.date_of_birth).toISOString().split('T')[0];
                        }

                        // Set default values for optional fields if they're empty
                        formData.emergency_contact = formData.emergency_contact || '';
                        formData.blood_type = formData.blood_type || '';
                        formData.medical_history = Array.isArray(formData.medical_history) ? formData.medical_history : [];
                        formData.allergies = Array.isArray(formData.allergies) ? formData.allergies : [];

                        let response;
                        const headers = {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        };

                        if (this.editingPatient) {
                            // Update existing patient
                            response = await axios.put(`/api/patients/${this.editingPatient.id}`, formData, { headers });
                            const index = this.patients.findIndex(p => p.id === this.editingPatient.id);
                            if (index !== -1) {
                                this.patients[index] = response.data;
                            }
                        } else {
                            // Create new patient
                            response = await axios.post('/api/patients', formData, { headers });
                            this.patients.unshift(response.data);
                        }

                        // Close modal and reset form
                        this.showAddPatientModal = false;
                        this.editingPatient = null;
                        this.resetPatientForm();

                        // Show success message
                        alert(this.editingPatient ? 'Patient updated successfully!' : 'Patient added successfully!');

                        // Refresh the patient list
                        await this.fetchPatients();
                    } catch (error) {
                        console.error('Error saving patient:', error);
                        if (error.response) {
                            if (error.response.status === 422) {
                                // Validation errors
                                const errors = error.response.data.errors;
                                const errorMessage = Object.values(errors).flat().join('\n');
                                alert('Validation errors:\n' + errorMessage);
                            } else if (error.response.status === 419) {
                                // CSRF token mismatch
                                alert('Session expired. Please refresh the page and try again.');
                                window.location.reload();
                            } else if (error.response.status === 401) {
                                // Unauthorized
                                alert('Please log in again to continue.');
                                window.location.href = '/login';
                            } else {
                                alert('Error saving patient: ' + (error.response.data.message || 'Please try again.'));
                            }
                        } else if (error.request) {
                            // Network error
                            alert('Network error. Please check your connection and try again.');
                        } else {
                            // Other error
                            alert('An unexpected error occurred. Please try again.');
                        }
                    }
                },
                resetPatientForm() {
                    this.patientForm = {
                        age: null,
                        gender: '',
                        condition: '',
                        procedure: '',
                        cost: null,
                        length_of_stay: null,
                        readmission: false,
                        outcome: '',
                        satisfaction: null
                    };
                },
                closePatientModal() {
                    this.showPatientModal = false;
                    this.selectedPatient = null;
                }
            }
        });
    </script>
</body>
</html>
