# Scripts

Utility scripts per la gestione del progetto Mobile Lookup.

## Script Disponibili

### update-urls.sh / update-urls.ps1

Aggiorna gli URL GitHub in tutti i file del progetto.

**Uso (Linux/Mac):**
```bash
chmod +x scripts/update-urls.sh
./scripts/update-urls.sh your-github-username
```

**Uso (Windows PowerShell):**
```powershell
.\scripts\update-urls.ps1 -Username "your-github-username"
```

Lo script:
1. Crea un backup dei file originali
2. Sostituisce `yourusername` con il tuo username
3. Mostra i file aggiornati

## Note

- Gli script creano automaticamente un backup prima di modificare i file
- Il backup viene salvato in una directory `.url-backup-TIMESTAMP`
- Dopo l'esecuzione, verifica le modifiche con `git diff`

## Aggiungere Nuovi Script

Quando aggiungi nuovi script:
1. Usa nomi descrittivi (es. `release.sh`, `test.sh`)
2. Aggiungi shebang per Bash: `#!/bin/bash`
3. Usa `set -e` per interrompere su errore
4. Aggiungi documentazione in questo README
5. Rendi eseguibile: `chmod +x scripts/your-script.sh`
