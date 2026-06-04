# Mobile Lookup Module for Xataface

[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html)
[![Xataface](https://img.shields.io/badge/Xataface-2.x-green.svg)](http://xataface.com/)
[![PHP](https://img.shields.io/badge/PHP-7.0%2B-777BB4.svg)](https://www.php.net/)
[![Select2](https://img.shields.io/badge/Select2-4.1.0-orange.svg)](https://select2.org/)

> Widget mobile-friendly per lookup record con ricerca AJAX e interfaccia touch-optimized per Xataface.

![Mobile Lookup Demo](docs/images/demo.gif)
<!-- TODO: Add actual screenshot -->

## 📱 Caratteristiche

- ✅ **Touch-optimized**: Target touch 44px (iOS standard)
- ✅ **Ricerca AJAX**: Caricamento dinamico risultati
- ✅ **Mobile-first**: Font-size 16px per evitare zoom iOS
- ✅ **Responsive**: Adattamento automatico mobile/tablet/desktop
- ✅ **Select2**: Libreria moderna e performante
- ✅ **Sicurezza**: Prepared statements e validazione input
- ✅ **Dark mode**: Supporto tema scuro (opzionale)

## 🚀 Installazione

### 1. Attiva modulo in conf.ini

```ini
[_modules]
    modules_mobile_lookup=modules/mobile_lookup/mobile_lookup.php
```

### 2. Riavvia applicazione

Riavvia PHP o pulisci cache Xataface per caricare il modulo.

## 📖 Utilizzo

### Basic Usage

In `tables/TuaTabella/fields.ini`:

```ini
[fkCliente]
    widget:type=mobile_lookup
    widget:table=BBGFATTClienti
    widget:keycol=cod
    widget:labelcol=rag
    widget:placeholder="Seleziona cliente..."
```

### Advanced Usage

```ini
[fkAgente]
    widget:type=mobile_lookup
    widget:table=BBGFATTAgenti
    widget:keycol=cod
    widget:labelcol=nome
    widget:searchFields=nome,cognome,codice
    widget:placeholder="Cerca agente per nome o codice..."
    widget:minimumInputLength=2
```

### Con Filtri Dinamici

```ini
[fkRicambio]
    widget:type=mobile_lookup
    widget:table=BBGRicambi
    widget:keycol=id
    widget:labelcol=descrizione
    widget:searchFields=descrizione,codice
    widget:filters[categoria]=$fkCategoria
    widget:placeholder="Seleziona ricambio..."
```

I filtri con `$nomeCampo` vengono risolti dinamicamente dal valore di altri campi nel form.

## ⚙️ Parametri Widget

| Parametro | Obbligatorio | Default | Descrizione |
|-----------|--------------|---------|-------------|
| `widget:table` | ✅ Sì | - | Nome tabella per lookup |
| `widget:keycol` | No | `id` | Colonna chiave primaria |
| `widget:labelcol` | No | `nome` | Colonna etichetta visualizzata |
| `widget:searchFields` | No | `labelcol` | Campi ricerca (comma-separated) |
| `widget:placeholder` | No | `"Seleziona..."` | Placeholder input |
| `widget:minimumInputLength` | No | `0` | Min caratteri per ricerca |
| `widget:preloadOptions` | No | `selected` | Preload iniziale: `selected` \| `first100` \| `none` |
| `widget:filters[campo]` | No | - | Filtri fissi o dinamici ($) |

## 🎨 Personalizzazione CSS

Puoi sovrascrivere gli stili in `css/custom.css`:

```css
/* Touch target più grande */
.select2-container--default .select2-selection--single {
    height: 52px !important;
}

/* Dropdown con bordo colorato */
.xf-mobile-lookup-dropdown {
    border: 2px solid #0056b3;
}
```

## 🔧 Troubleshooting

### Widget non appare
- Verifica che il modulo sia attivato in `conf.ini`
- Controlla console browser per errori JS
- Verifica permessi `view` sulla tabella lookup

### Ricerca non funziona
- Controlla `actions/mobile_lookup_search.php` sia eseguibile
- Verifica log PHP per errori database
- Testa URL AJAX manualmente: `?-action=mobile_lookup_search&-table=Nome`

### Su mobile non è touch-friendly
- Pulisci cache browser
- Verifica che `mobile-lookup.css` sia caricato (Dev Tools > Network)
- Font-size input deve essere >= 16px per evitare zoom iOS

## 🆚 Mobile Lookup vs Lookup Standard

| Caratteristica | Lookup Standard | Mobile Lookup |
|----------------|-----------------|---------------|
| Mobile-friendly | ❌ No | ✅ Sì |
| Touch targets | Piccoli | 44px iOS standard |
| Ricerca | Client-side | AJAX server-side |
| Paginazione | No | Sì (30 record/pagina) |
| Zoom iOS | Sì (font <16px) | No (font 16px) |
| Responsive | Limitato | Full responsive |
| Performance | OK | Ottimizzata |

## 📝 Note Sviluppo

- **Select2 versione**: 4.1.0-rc.0 (da CDN)
- **Compatibilità**: Xataface 2.x, PHP 7.0+
- **Database**: Usa connessione nativa Xataface `df_db()`
- **Sicurezza**: Validazione parametri, sanitizzazione SQL, check permessi
- **Performance grid/edit**: usare `widget:preloadOptions=selected` (default) o `none` per evitare preload massivo di opzioni in form con molte righe

## 🔜 Roadmap

- [ ] Supporto multi-select
- [ ] Template custom per risultati
- [ ] Cache risultati ricerca
- [ ] Infinite scroll invece di paginazione
- [ ] Supporto immagini nei risultati

## 👤 Autore

Paolo Bonzini - Febbraio 2026

## 📄 Licenza

Questo progetto è distribuito con licenza [GNU GPL v2](LICENSE), compatibile con Xataface.

## 🤝 Contributing

I contributi sono benvenuti! Leggi [CONTRIBUTING.md](CONTRIBUTING.md) per le linee guida.

## 🐛 Bug Reports

Per segnalare bug o richiedere nuove funzionalità, apri una [issue su GitHub](https://github.com/plcats/module-mobile_lookup/issues).

## ⭐ Supporto

Se questo modulo ti è stato utile, considera di mettere una stella ⭐ al repository!

---

Realizzato con ❤️ da [Paolo Bonzini](https://github.com/plcats)
