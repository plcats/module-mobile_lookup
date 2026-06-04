# Script PowerShell per aggiornare gli URL GitHub nel progetto
# Uso: .\scripts\update-urls.ps1 -Username "your-github-username"

param(
    [Parameter(Mandatory=$true)]
    [string]$Username
)

$ErrorActionPreference = "Stop"

$OldUrl = "yourusername"

Write-Host "🔄 Aggiornamento URL GitHub..." -ForegroundColor Cyan
Write-Host "   Da: $OldUrl" -ForegroundColor Gray
Write-Host "   A:  $Username" -ForegroundColor Green
Write-Host ""

# Lista dei file da aggiornare
$Files = @(
    "README.md",
    "CONTRIBUTING.md",
    "INSTALL.md",
    "composer.json",
    "docs\EXAMPLES.md",
    "docs\README.md",
    "CHANGELOG.md",
    "GITHUB_PREPARATION_SUMMARY.md"
)

# Backup directory
$BackupDir = ".url-backup-$(Get-Date -Format 'yyyyMMdd-HHmmss')"
New-Item -ItemType Directory -Path $BackupDir -Force | Out-Null

Write-Host "📦 Creazione backup in $BackupDir\" -ForegroundColor Yellow

# Backup e aggiornamento
foreach ($file in $Files) {
    if (Test-Path $file) {
        Write-Host "   ✓ $file" -ForegroundColor Green
        
        # Backup
        Copy-Item $file -Destination $BackupDir
        
        # Aggiorna URL
        $content = Get-Content $file -Raw
        $newContent = $content -replace [regex]::Escape($OldUrl), $Username
        Set-Content $file -Value $newContent -NoNewline
    }
    else {
        Write-Host "   ⚠ $file non trovato" -ForegroundColor Yellow
    }
}

Write-Host ""
Write-Host "✅ Aggiornamento completato!" -ForegroundColor Green
Write-Host "   Backup salvato in: $BackupDir\" -ForegroundColor Gray
Write-Host ""
Write-Host "Verifica le modifiche con:" -ForegroundColor Cyan
Write-Host "   git diff" -ForegroundColor White
Write-Host ""
Write-Host "Se tutto è ok, committa:" -ForegroundColor Cyan
Write-Host "   git add ." -ForegroundColor White
Write-Host "   git commit -m `"Update GitHub URLs to $Username`"" -ForegroundColor White
