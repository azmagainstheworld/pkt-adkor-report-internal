@echo off
title Reverb Server
:loop
echo Memulai server Reverb...
php artisan reverb:start
echo Reverb terhenti. Merestart dalam 2 detik...
timeout /t 2 >nul
goto loop
