# Pubblicare aggiornamento v1.1.0 su GitHub

Repository: https://github.com/plcats/module-mobile_lookup

Modulo locale: `TEST/INT/BBGPOINT/modules/mobile_lookup/`

---

## Scenario A — Repository GitHub già esistente (aggiornamento)

### 1. Clona (se non hai già una copia git del modulo)

```powershell
cd C:\Users\paolo\Sviluppo\dev
git clone https://github.com/plcats/module-mobile_lookup.git
cd module-mobile_lookup
```

### 2. Copia i file aggiornati da BBG Point

```powershell
robocopy "C:\Users\paolo\Sviluppo\Xataface\TEST\INT\BBGPOINT\modules\mobile_lookup" "." /E /XD .git /XF GITHUB_PREPARATION_SUMMARY.md GITHUB_UPDATE.md
```

Oppure, se lavori direttamente nella cartella del modulo:

```powershell
cd C:\Users\paolo\Sviluppo\Xataface\TEST\INT\BBGPOINT\modules\mobile_lookup
git init
git remote add origin https://github.com/plcats/module-mobile_lookup.git
git fetch origin
git checkout -b main origin/main
```

> Se `git init` è nuovo e il repo remoto ha già storia, preferisci **Scenario A step 1-2** (clone + robocopy) per evitare conflitti.

### 3. Verifica, commit, push

```powershell
git status
git add .
git commit -m "Release v1.1.0: multi-word search, auto-focus, docs update"
git push origin main
```

### 4. Tag e release

```powershell
git tag -a v1.1.0 -m "v1.1.0 - Multi-word search, auto-focus on open"
git push origin v1.1.0
```

### 5. Crea release su GitHub (CLI)

```powershell
gh release create v1.1.0 --title "v1.1.0" --notes-file CHANGELOG.md
```

Oppure manualmente: GitHub → Releases → Draft new release → tag `v1.1.0` → incolla sezione `[1.1.0]` da `CHANGELOG.md`.

---

## Scenario B — Prima pubblicazione (repo vuoto su GitHub)

```powershell
cd C:\Users\paolo\Sviluppo\Xataface\TEST\INT\BBGPOINT\modules\mobile_lookup
git init
git add .
git commit -m "Release v1.1.0 - Mobile Lookup Module"
git branch -M main
git remote add origin https://github.com/plcats/module-mobile_lookup.git
git push -u origin main
git tag -a v1.1.0 -m "v1.1.0 - Multi-word search, auto-focus on open"
git push origin v1.1.0
gh release create v1.1.0 --title "v1.1.0" --notes "Vedi CHANGELOG.md sezione 1.1.0"
```

---

## File modificati in v1.1.0 (checklist)

| File | Modifica |
|------|----------|
| `actions/mobile_lookup_search.php` | Ricerca multi-parola AND |
| `js/mobile-lookup.js` | Auto-focus ricerca all'apertura |
| `version.txt` | `1.1.0` |
| `composer.json` | version `1.1.0` |
| `mobile_lookup.php` | @version 1.1.0 |
| `README.md` | Ricerca, allscreen, troubleshooting |
| `CHANGELOG.md` | Sezione 1.1.0 |
| `INSTALL.md` | Search behavior, auto-focus |
| `docs/EXAMPLES.md` | Esempi ricerca e allscreen varianti |

---

## Autenticazione push

**HTTPS con token:**

```powershell
git remote set-url origin https://TUO_TOKEN@github.com/plcats/module-mobile_lookup.git
```

**SSH:**

```powershell
git remote set-url origin git@github.com:plcats/module-mobile_lookup.git
```

---

## Dopo il push

1. Verifica su GitHub che `version.txt` mostri `1.1.0`
2. Controlla tab Releases → `v1.1.0`
3. Aggiorna installazioni esistenti: sostituire cartella `modules/mobile_lookup` e svuotare cache browser
