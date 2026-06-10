# 🚀 Quick Start - Pubblicazione su GitHub

Guida rapida in 10 minuti per pubblicare il modulo Mobile Lookup su GitHub.

## ⚡ Setup Veloce (10 minuti)

### 1️⃣ Aggiorna URL GitHub (2 min)

**Windows:**
```powershell
cd TEST\INT\BBGPOINT\modules\mobile_lookup
.\scripts\update-urls.ps1 -Username "plcats"
```

**Linux/Mac:**
```bash
cd TEST/INT/BBGPOINT/modules/mobile_lookup
chmod +x scripts/update-urls.sh
./scripts/update-urls.sh plcats
```

### 2️⃣ Inizializza Git (1 min)

```bash
git init
git add .
git commit -m "Release v1.1.0 - Mobile Lookup Module"
```

### 3️⃣ Crea Repository su GitHub (2 min)

1. Vai su https://github.com/new
2. Nome: `module-mobile_lookup`
3. Descrizione: `Mobile-friendly lookup widget for Xataface with AJAX search`
4. **Pubblico**
5. **NON** aggiungere README/License/.gitignore
6. Crea repository

### 4️⃣ Push su GitHub (1 min)

```bash
git remote add origin https://github.com/plcats/module-mobile_lookup.git
git branch -M main
git push -u origin main
```

### 5️⃣ Configura Repository (2 min)

**Topics (tag):**
- xataface
- widget  
- mobile
- lookup
- select2
- php
- javascript

**About:**
- ✅ Include releases
- Website: (opzionale)

### 6️⃣ Crea Prima Release (2 min)

```bash
git tag -a v1.1.0 -m "Release v1.1.0 - multi-word search, auto-focus"
git push origin v1.1.0
```

Su GitHub:
1. Releases → Create a new release
2. Tag: `v1.1.0`
3. Title: `Version 1.1.0`
4. Description: copia da CHANGELOG.md
5. Publish release

---

## ✅ Checklist Minima

Prima di pubblicare, assicurati di aver fatto:

- [x] ✏️ Aggiornato URL (step 1)
- [x] 🔧 Testato il modulo
- [ ] 📸 Aggiunto almeno 1 screenshot in `docs/images/`
- [ ] 🔗 Verificato tutti i link nel README
- [ ] 📝 Riletto README.md e INSTALL.md

---

## 📸 Screenshot Opzionali (ma consigliati)

Crea screenshot e salvali in `docs/images/`:

```bash
docs/images/
├── demo.gif              # Demo animata (opzionale ma wow!)
├── screenshot-mobile.png # Vista mobile
└── screenshot-desktop.png # Vista desktop
```

Poi aggiorna il README.md sostituendo:
```markdown
![Mobile Lookup Demo](docs/images/demo.gif)
<!-- TODO: Add actual screenshot -->
```

con:
```markdown
![Mobile Lookup Demo](docs/images/demo.gif)
```

---

## 🎯 Dopo la Pubblicazione

### Annuncia il Progetto

1. **Xataface Forum**: Posta su http://xataface.com/forum/
2. **Reddit**: r/PHP, r/webdev (se appropriato)
3. **Twitter/X**: Tweet con #Xataface #PHP
4. **LinkedIn**: Post professionale

### Monitora Feedback

- ⭐ GitHub Stars
- 🐛 GitHub Issues  
- 💬 Discussioni/Commenti
- 📊 Traffic (Insights → Traffic)

---

## 🆘 Problemi Comuni

### "Permission denied" durante push

```bash
# Usa HTTPS con token
git remote set-url origin https://TUO_TOKEN@github.com/plcats/module-mobile_lookup.git

# O configura SSH
git remote set-url origin git@github.com:plcats/module-mobile_lookup.git
```

### File troppo grandi

```bash
# Rimuovi file dalla history
git rm --cached file-grande
git commit --amend
```

### Dimenticato di aggiornare URL

```bash
# Riavvia lo script update-urls
./scripts/update-urls.sh plcats
git add .
git commit -m "Fix: Update GitHub URLs"
git push
```

---

## 📚 Link Utili

- 📖 [Aggiornamento GitHub v1.1.0](GITHUB_UPDATE.md)
- 📖 [Guida Completa](GITHUB_PREPARATION_SUMMARY.md)
- 🔧 [Installazione](INSTALL.md)
- 💡 [Esempi](docs/EXAMPLES.md)
- 🤝 [Contributing](CONTRIBUTING.md)
- 📋 [Release Process](RELEASE.md)

---

## 🎉 Fatto!

Il tuo modulo è ora su GitHub! 🚀

**Prossimi passi:**
1. Aggiungi screenshot
2. Monitora GitHub Issues
3. Rispondi alle domande della community
4. Accetta pull request
5. Rilascia nuove versioni

**Congratulazioni!** 🎊
