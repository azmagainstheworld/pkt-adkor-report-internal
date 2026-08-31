@echo off
title Laravel Serve
:loop
echo Memulai server Laravel...
php artisan serve
echo Server terhenti. Merestart dalam 2 detik...
timeout /t 2 >nul
goto loop
