# start_server.ps1 - PowerShell script to start XSS Learning Lab
$ErrorActionPreference = "Stop"

Write-Host "=======================================================" -ForegroundColor Cyan
Write-Host "   🛡️ XSS Security Lab - 学習用サーバー起動スクリプト   " -ForegroundColor Cyan
Write-Host "=======================================================" -ForegroundColor Cyan
Write-Host ""

$phpPath = "php"
if (-not (Get-Command php -ErrorAction SilentlyContinue)) {
    if (Test-Path "C:\xampp\php\php.exe") {
        $phpPath = "C:\xampp\php\php.exe"
    } else {
        Write-Host "[エラー] PHPが見つかりませんでした。" -ForegroundColor Red
        Exit 1
    }
}

Write-Host "[情報] PHPパス: $phpPath" -ForegroundColor Green
Write-Host "[情報] サーバーURL: http://localhost:8080" -ForegroundColor Green
Write-Host "[情報] 終了するには Ctrl+C を押してください。" -ForegroundColor Yellow
Write-Host ""

Start-Process "http://localhost:8080"
& $phpPath -S localhost:8080 -t public
