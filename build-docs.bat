@echo off
echo Installation des dépendances...
pip install -r requirements-docs.txt

echo Build de la documentation...
mkdocs build

echo Documentation générée dans le dossier 'site'
pause
