# Dossier `/public` - Assets Publics

Le dossier `/public` est le point d'entrée web de l'application et contient tous les fichiers accessibles directement par les navigateurs.

## Structure

```
public/
├── index.php              # Point d'entrée principal
├── login.php              # Page de connexion
├── profile.php            # Page de profil utilisateur
├── contact.php            # Page de contact
├── activites.php          # Page des activités
├── assets/                # Assets statiques
├── activite/              # Assets spécifiques aux activités
├── cache/                 # Cache web
├── data/                  # Données publiques
└── fonts/                 # Polices de caractères
```

## Pages Principales

### `index.php`
- Point d'entrée principal de l'application
- Page d'accueil
- Routage initial

### Pages Fonctionnelles
- `login.php` : Authentification utilisateur
- `profile.php` : Gestion du profil utilisateur
- `contact.php` : Formulaire de contact
- `activites.php` : Liste et gestion des activités

## `/public/assets` - Assets Statiques

### Structure Typique
```
assets/
├── css/                   # Feuilles de style compilées
├── js/                    # Scripts JavaScript
├── images/                # Images et icônes
└── uploads/               # Fichiers uploadés par les utilisateurs
```

**Contenu** :
- CSS compilé par Vite (module.css)
- Scripts JavaScript
- Images, icônes, logos
- Fichiers uploadés par les utilisateurs

## Configuration Serveur Web

### Apache (.htaccess)
Le dossier public devrait être configuré comme DocumentRoot du serveur web.

### Sécurité
- Seuls les fichiers de ce dossier sont accessibles publiquement
- Les dossiers `App/`, `config/`, etc. sont protégés
- Validation des uploads et types MIME

## Assets Compilés

### CSS (via Vite)
- Les fichiers CSS sources dans `/src` sont compilés vers `/dist`
- Puis copiés dans `/public/assets/css/` pour être servis
- Utilisation de Tailwind CSS + DaisyUI

### Processus de Build
1. Sources dans `/src/main.css`
2. Compilation par Vite vers `/dist/module.css`
3. Copie vers `/public/assets/css/module.css`
4. Référencement dans les templates Blade

## Cache Public

### `/public/cache`
- Cache côté client
- Assets optimisés
- Fichiers temporaires web

## Bonnes Pratiques

### Performance
- Compression gzip/brotli activée
- Headers de cache appropriés
- Optimisation des images
- Minification des assets

### Sécurité
- Validation des uploads
- Protection contre les injections
- Headers de sécurité appropriés
- Limitation des types de fichiers
