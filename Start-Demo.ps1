param([int]$Port = 8000)
$ErrorActionPreference = 'Stop'
Set-Location $PSScriptRoot
if (-not (Get-Command php -ErrorAction SilentlyContinue)) { throw 'PHP must be on PATH.' }
if (-not (Test-Path vendor/autoload.php)) { throw 'Run composer install first.' }
if (Test-Path .env) { throw 'An existing .env was found. Follow the README to run that configured instance; this script never replaces it.' }
$demoDatabase = Join-Path $PSScriptRoot 'database/portfolio-demo.sqlite'
if (Test-Path $demoDatabase) { throw 'A demo database already exists. Use your existing configuration; this script does not reset data.' }
New-Item -ItemType File -Path $demoDatabase | Out-Null
$demoConfig = Get-Content .env.example -Raw
$demoConfig = $demoConfig -replace 'DB_CONNECTION=mysql', 'DB_CONNECTION=sqlite'
$demoConfig = $demoConfig -replace 'DB_DATABASE=material_request', ('DB_DATABASE="' + $demoDatabase.Replace('\', '/') + '"')
Set-Content -LiteralPath .env -Value $demoConfig -Encoding utf8
php artisan key:generate
if ($LASTEXITCODE -ne 0) { throw 'Key generation failed.' }
php artisan migrate --seed
if ($LASTEXITCODE -ne 0) { throw 'Demo database setup failed.' }
Write-Host "Local demo: http://127.0.0.1:$Port — admin / password123"
php artisan serve --host=127.0.0.1 --port=$Port
