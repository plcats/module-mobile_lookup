#!/bin/bash
# Script per aggiornare gli URL GitHub nel progetto
# Uso: ./scripts/update-urls.sh your-github-username

set -e

if [ -z "$1" ]; then
    echo "❌ Errore: specificare il tuo username GitHub"
    echo "Uso: $0 YOUR_GITHUB_USERNAME"
    exit 1
fi

USERNAME="$1"
OLD_URL="yourusername"

echo "🔄 Aggiornamento URL GitHub..."
echo "   Da: $OLD_URL"
echo "   A:  $USERNAME"
echo ""

# Lista dei file da aggiornare
FILES=(
    "README.md"
    "CONTRIBUTING.md"
    "INSTALL.md"
    "composer.json"
    "docs/EXAMPLES.md"
    "docs/README.md"
    "CHANGELOG.md"
    "GITHUB_PREPARATION_SUMMARY.md"
)

# Backup directory
BACKUP_DIR=".url-backup-$(date +%Y%m%d-%H%M%S)"
mkdir -p "$BACKUP_DIR"

echo "📦 Creazione backup in $BACKUP_DIR/"

# Backup e aggiornamento
for file in "${FILES[@]}"; do
    if [ -f "$file" ]; then
        echo "   ✓ $file"
        cp "$file" "$BACKUP_DIR/"
        
        # Aggiorna URL
        if [[ "$OSTYPE" == "darwin"* ]]; then
            # macOS
            sed -i '' "s/$OLD_URL/$USERNAME/g" "$file"
        else
            # Linux
            sed -i "s/$OLD_URL/$USERNAME/g" "$file"
        fi
    else
        echo "   ⚠ $file non trovato"
    fi
done

echo ""
echo "✅ Aggiornamento completato!"
echo "   Backup salvato in: $BACKUP_DIR/"
echo ""
echo "Verifica le modifiche con:"
echo "   git diff"
echo ""
echo "Se tutto è ok, committa:"
echo "   git add ."
echo "   git commit -m \"Update GitHub URLs to $USERNAME\""
