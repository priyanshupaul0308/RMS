@echo off
title Start MySQL Service
net session >nul 2>&1
if %errorLevel% == 0 (
    goto :admin
) else (
    echo Requesting Administrator privileges to start MySQL...
    powershell -ExecutionPolicy Bypass -Command "Start-Process '%~f0' -Verb RunAs"
    exit /b
)

:admin
cd /d "%~dp0"
echo ==============================================
echo   Starting MySQL 8.0 Service (MYSQL80)...
echo ==============================================
echo.

net start MYSQL80
if %errorLevel% equ 0 (
    sc config MYSQL80 start= auto >nul 2>&1
    echo.
    echo ==============================================
    echo   [SUCCESS] MySQL 8.0 is running!
    echo   Configured to start automatically on boot.
    echo ==============================================
) else (
    echo.
    echo [ERROR] Failed to start MYSQL80 service.
)

echo.
echo You may close this window.
timeout /t 5
