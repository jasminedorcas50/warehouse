@echo off
cd /d %~dp0
echo Starting Laravel Queue Worker...
php artisan queue:work --tries=3 --timeout=300 --sleep=3 --max-jobs=1000 --max-time=3600
