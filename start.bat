@echo off

cd /d C:\laragon\www\kocert

start "" cmd /k "cd app && ng serve --port 4200"
start "" cmd /k "cd admin && ng serve --port 5000"
start "" cmd /k "cd api && php artisan serve"

exit