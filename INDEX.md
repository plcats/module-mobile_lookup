# 📚 Mobile Lookup Module - Document Index

Indice completo di tutta la documentazione del progetto Mobile Lookup.

---

## 🚀 Getting Started (Inizia qui!)

| Documento | Tempo | Descrizione |
|-----------|-------|-------------|
| **[README.md](README.md)** | 5 min | Panoramica, features, utilizzo base |
| **[QUICK_START_GITHUB.md](QUICK_START_GITHUB.md)** | 10 min | Guida veloce pubblicazione GitHub |
| **[INSTALL.md](INSTALL.md)** | 15 min | Installazione dettagliata e configurazione |

---

## 📖 Documentazione Utente

### Utilizzo
| Documento | Contenuto |
|-----------|-----------|
| [README.md](README.md) | Features, parametri, esempi base |
| [docs/EXAMPLES.md](docs/EXAMPLES.md) | Esempi real-world, casi d'uso complessi |
| [INSTALL.md](INSTALL.md) | Guida installazione completa |

### Troubleshooting
- **README.md** → Sezione "🔧 Troubleshooting"
- **INSTALL.md** → Sezione "Troubleshooting"
- **docs/README.md** → Sezione "Troubleshooting"

---

## 👨‍💻 Documentazione Developer

### Architettura
| Documento | Contenuto |
|-----------|-----------|
| [PROJECT_STRUCTURE.md](PROJECT_STRUCTURE.md) | Struttura progetto, file descriptions |
| [docs/README.md](docs/README.md) | Architettura tecnica, data flow |
| [CONTRIBUTING.md](CONTRIBUTING.md) | Guidelines sviluppo |

### API Reference
- **docs/README.md** → Sezione "API Reference"
- **README.md** → Tabella "⚙️ Parametri Widget"

### Sviluppo
| Documento | Contenuto |
|-----------|-----------|
| [CONTRIBUTING.md](CONTRIBUTING.md) | Come contribuire, style guide |
| [RELEASE.md](RELEASE.md) | Processo rilascio versioni |
| [scripts/README.md](scripts/README.md) | Utility scripts |

---

## 🔧 Configurazione e Setup

| Documento | Fase | Descrizione |
|-----------|------|-------------|
| [INSTALL.md](INSTALL.md) | Setup | Installazione modulo |
| [README.md](README.md) | Configurazione | Parametri widget |
| [docs/EXAMPLES.md](docs/EXAMPLES.md) | Esempi | Configurazioni pratiche |

---

## 📝 Amministrazione Progetto

### Rilasci
| Documento | Scopo |
|-----------|-------|
| [CHANGELOG.md](CHANGELOG.md) | Storia versioni e modifiche |
| [RELEASE.md](RELEASE.md) | Processo di release |
| [version.txt](version.txt) | Numero versione corrente |

### GitHub
| Documento | Scopo |
|-----------|-------|
| [GITHUB_PREPARATION_SUMMARY.md](GITHUB_PREPARATION_SUMMARY.md) | Riepilogo preparazione completa |
| [QUICK_START_GITHUB.md](QUICK_START_GITHUB.md) | Guida rapida pubblicazione |
| [.github/ISSUE_TEMPLATE/bug_report.md](.github/ISSUE_TEMPLATE/bug_report.md) | Template bug report |
| [.github/ISSUE_TEMPLATE/feature_request.md](.github/ISSUE_TEMPLATE/feature_request.md) | Template feature request |
| [.github/PULL_REQUEST_TEMPLATE.md](.github/PULL_REQUEST_TEMPLATE.md) | Template pull request |

### CI/CD
| Documento | Scopo |
|-----------|-------|
| [.github/workflows/ci.yml](.github/workflows/ci.yml) | Continuous Integration |
| [.github/workflows/release.yml](.github/workflows/release.yml) | Release automation |

---

## 🔍 Riferimenti Rapidi

### Cheat Sheets

#### Parametri Widget (Base)
```ini
widget:type=mobile_lookup
widget:table=TableName
widget:keycol=id
widget:labelcol=name
```

#### Parametri Widget (Avanzato)
```ini
widget:searchFields=name,code
widget:filters[category]=$category_id
widget:preloadOptions=selected
widget:allscreen=1
```

#### Script Comuni
```bash
# Aggiorna URL GitHub
./scripts/update-urls.sh USERNAME

# Crea release
git tag -a v1.0.0 -m "Release 1.0.0"
git push origin v1.0.0
```

---

## 📋 Checklist e Verifiche

| Documento | Quando Usarlo |
|-----------|---------------|
| [VERIFICATION_CHECKLIST.md](VERIFICATION_CHECKLIST.md) | Prima di ogni release |
| [GITHUB_PREPARATION_SUMMARY.md](GITHUB_PREPARATION_SUMMARY.md) | Prima pubblicazione GitHub |
| [RELEASE.md](RELEASE.md) | Durante processo di release |

---

## 📦 File Configurazione

| File | Scopo |
|------|-------|
| [composer.json](composer.json) | Metadati pacchetto |
| [.gitignore](.gitignore) | File da ignorare |
| [.gitattributes](.gitattributes) | Line endings, export rules |
| [actions.ini.php](actions.ini.php) | Registrazione azioni Xataface |

---

## 📄 File Legali

| File | Contenuto |
|------|-----------|
| [LICENSE](LICENSE) | GNU GPL v2 License |
| **README.md** → Sezione "📄 Licenza" | Info licenza |

---

## 🎓 Percorsi di Apprendimento

### Nuovo Utente
1. [README.md](README.md) - Overview
2. [INSTALL.md](INSTALL.md) - Installazione
3. [docs/EXAMPLES.md](docs/EXAMPLES.md) - Primi esempi
4. Test su tua applicazione

### Nuovo Developer
1. [README.md](README.md) - Funzionalità
2. [PROJECT_STRUCTURE.md](PROJECT_STRUCTURE.md) - Architettura
3. [CONTRIBUTING.md](CONTRIBUTING.md) - Guidelines
4. [docs/README.md](docs/README.md) - Dettagli tecnici
5. Esplora codice sorgente

### Maintainer
1. [RELEASE.md](RELEASE.md) - Processo release
2. [VERIFICATION_CHECKLIST.md](VERIFICATION_CHECKLIST.md) - Checklist
3. [GITHUB_PREPARATION_SUMMARY.md](GITHUB_PREPARATION_SUMMARY.md) - Setup GitHub
4. [CHANGELOG.md](CHANGELOG.md) - Documenta modifiche

---

## 🔗 Link Esterni Utili

- [Xataface Documentation](http://xataface.com/documentation)
- [Select2 Documentation](https://select2.org/)
- [Keep a Changelog](https://keepachangelog.com/)
- [Semantic Versioning](https://semver.org/)
- [GitHub Docs](https://docs.github.com/)
- [PHP Documentation](https://www.php.net/docs.php)

---

## 🗂️ Struttura Directory

```
mobile_lookup/
├── 📄 Documentazione Root
│   ├── README.md                    ⭐ Inizia qui
│   ├── INSTALL.md                   📦 Installazione
│   ├── CHANGELOG.md                 📋 Storia versioni
│   ├── CONTRIBUTING.md              🤝 Come contribuire
│   ├── LICENSE                      ⚖️ Licenza
│   └── INDEX.md                     📚 Questo file
│
├── 📂 docs/                         📖 Documentazione estesa
│   ├── README.md                    🏗️ Architettura
│   ├── EXAMPLES.md                  💡 Esempi pratici
│   └── images/                      🖼️ Screenshot
│
├── 📂 .github/                      🐙 GitHub config
│   ├── ISSUE_TEMPLATE/              🐛 Template issue
│   └── workflows/                   ⚙️ CI/CD
│
├── 📂 scripts/                      🔧 Utility scripts
│   ├── update-urls.sh               🔗 Aggiorna URL
│   └── update-urls.ps1              🔗 Aggiorna URL (Win)
│
├── 📂 actions/                      🎬 Xataface actions
├── 📂 css/                          🎨 Styles
├── 📂 js/                           💻 JavaScript
├── 📂 HTML/QuickForm/               📝 QuickForm elements
│
└── 🔧 File Configurazione
    ├── composer.json
    ├── .gitignore
    └── .gitattributes
```

---

## 🎯 Quick Navigation

### Voglio...

- **Installare il modulo** → [INSTALL.md](INSTALL.md)
- **Vedere esempi** → [docs/EXAMPLES.md](docs/EXAMPLES.md)
- **Capire come funziona** → [docs/README.md](docs/README.md)
- **Contribuire** → [CONTRIBUTING.md](CONTRIBUTING.md)
- **Pubblicare su GitHub** → [QUICK_START_GITHUB.md](QUICK_START_GITHUB.md)
- **Fare una release** → [RELEASE.md](RELEASE.md)
- **Risolvere un problema** → README.md → Troubleshooting
- **Vedere la licenza** → [LICENSE](LICENSE)
- **Vedere le modifiche** → [CHANGELOG.md](CHANGELOG.md)

---

## 📊 Statistiche Documentazione

| Tipo | Quantità |
|------|----------|
| 📄 File Markdown | 20+ |
| 📝 Guide Utente | 3 |
| 👨‍💻 Guide Developer | 5 |
| 🔧 Script Utility | 2 |
| 🐙 GitHub Templates | 5 |
| 📖 Totale Pagine | ~150 |

---

## 💡 Suggerimenti

- **Nuovo al progetto?** Inizia da [README.md](README.md)
- **Vuoi contribuire?** Leggi [CONTRIBUTING.md](CONTRIBUTING.md)
- **Problemi?** Controlla la sezione Troubleshooting in README.md o INSTALL.md
- **Domande?** Apri una issue su GitHub
- **Suggerimenti?** Benvenuti via pull request!

---

**Mantieni questo indice aggiornato** quando aggiungi nuova documentazione!

Ultimo aggiornamento: Febbraio 2026  
Versione modulo: 1.0.0
