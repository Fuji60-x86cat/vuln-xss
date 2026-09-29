@echo off
chcp 65001 > nul
setlocal

echo =======================================================
echo    🛡️ XSS Security Lab - 学習用サーバー起動スクリプト
echo =======================================================
echo.

:: Detect PHP
set PHP_BIN=php
where php >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    if exist "C:\xampp\php\php.exe" (
        set PHP_BIN=C:\xampp\php\php.exe
    ) else (
        echo [エラー] PHPが見つかりませんでした。
        echo XAMPPまたはPHPをインストールしてください。
        pause
        exit /b 1
    )
)

echo [情報] PHP実行ファイル: %PHP_BIN%
echo [情報] サーバー起動中: http://localhost:8080
echo [情報] サーバーを終了するには このウィンドウで Ctrl+C を押してください。
echo.

start "" "http://localhost:8080"
"%PHP_BIN%" -S localhost:8080 -t public

pause
