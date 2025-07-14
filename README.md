# Healthcare Data Warehouse System

A comprehensive healthcare data warehouse system built with Laravel, designed for analysis and decision support in healthcare organizations.

## Features

- **Patient Management**
  - Complete patient records management
  - Medical history tracking
  - Demographic analysis
  - Visit tracking

- **Medical Records**
  - Electronic health records (EHR)
  - Treatment tracking
  - Diagnosis management
  - Prescription history

- **Data Warehouse**
  - Real-time analytics
  - Trend analysis
  - Predictive analytics
  - Custom reporting

- **Analytics Dashboard**
  - Key performance indicators
  - Patient demographics
  - Visit trends
  - Treatment effectiveness
  - Custom visualizations

## Technology Stack

- **Backend**
  - Laravel 10.x
  - PHP 8.1+
  - MySQL 8.0+
  - RESTful API

- **Frontend**
  - Vue.js 2.6
  - Tailwind CSS
  - Chart.js
  - Axios

## Requirements

- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL >= 8.0
- Web server (Apache/Nginx)

## Installation

1. Clone the repository:
   ```bash
   git clone [repository-url]
   cd healthcare-warehouse
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install JavaScript dependencies:
   ```bash
   npm install
   ```

4. Create environment file:
   ```bash
   cp .env.example .env
   ```

5. Generate application key:
   ```bash
   php artisan key:generate
   ```

6. Configure your database in `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=healthcare_warehouse
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

7. Run migrations:
   ```bash
   php artisan migrate
   ```

8. Start the development server:
   ```bash
   php artisan serve
   ```

9. In a separate terminal, compile assets:
   ```bash
   npm run dev
   ```

## Usage

1. Access the application at `http://localhost:8000`
2. Log in with your credentials
3. Navigate through the dashboard to access different features
4. Use the analytics section for data analysis
5. Generate reports as needed

## Security Features

- Role-based access control
- Data encryption
- Audit logging
- HIPAA compliance measures
- Secure API endpoints

## API Documentation

The API documentation is available at `/api/documentation` when running the application.

Key endpoints:
- `/api/analytics/dashboard` - Dashboard metrics
- `/api/analytics/patient-analytics` - Patient analytics
- `/api/patients` - Patient management
- `/api/medical-records` - Medical records management
- `/api/warehouse/metrics` - Data warehouse metrics

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, please contact [support@healthcare-warehouse.com](mailto:support@healthcare-warehouse.com)
