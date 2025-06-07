# DaisyUI et Tailwind CSS

Cette documentation explique l'utilisation de **Tailwind CSS** et **DaisyUI** dans le projet, leur fonctionnement et l'importance de Node.js pour la compilation.

!!! info "Stack CSS moderne"
    Le projet utilise Tailwind CSS 4.x avec DaisyUI 5.x pour un développement UI rapide et cohérent.

## 🎨 Tailwind CSS - Framework utility-first

!!! abstract "Principe de base"
    **Tailwind CSS** est un framework CSS "utility-first" qui fournit des classes utilitaires de bas niveau pour construire des interfaces rapidement.

Au lieu d'écrire du CSS personnalisé, vous utilisez des classes prédéfinies directement dans le HTML :

```html
<!-- Approche traditionnelle CSS -->
<div class="ma-carte-personnalisee">Contenu</div>

<!-- Approche Tailwind -->
<div class="bg-white p-6 rounded-lg shadow-md">Contenu</div>
```

### Avantages
- **Rapidité** : Plus besoin d'écrire de CSS personnalisé
- **Consistance** : Système de design uniforme
- **Maintenance** : Moins de CSS à maintenir
- **Performance** : Purge automatique du CSS non utilisé

---

## 🌼 Qu'est-ce que DaisyUI ?

**DaisyUI** est une librairie de composants pour Tailwind CSS qui ajoute des classes de composants semantiques.

### Principe
DaisyUI transforme les classes utilitaires de Tailwind en composants réutilisables :

```html
<!-- Avec Tailwind seul -->
<button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
  Bouton
</button>

<!-- Avec DaisyUI -->
<button class="btn btn-primary">Bouton</button>
```

### Composants disponibles
- **Boutons** : `btn`, `btn-primary`, `btn-secondary`
- **Cartes** : `card`, `card-body`
- **Navigation** : `navbar`, `menu`
- **Formulaires** : `input`, `select`, `checkbox`
- **Etc** : modales, alertes, badges, ...

---

## 🛠️ Pourquoi Node.js est nécessaire ?

Node.js est **indispensable** pour utiliser Tailwind CSS et DaisyUI car il gère :

### 1. **Compilation et optimisation**
```bash
# Compile les styles Tailwind
npm run build

# Mode développement avec rechargement automatique
npm run dev
```

### 2. **Purge du CSS inutilisé**
Tailwind génère des milliers de classes CSS. Node.js :
- **Analyse** vos fichiers HTML/PHP/JS
- **Détecte** quelles classes sont utilisées
- **Supprime** le CSS non utilisé
- **Résultat** : Un fichier CSS final de quelques Ko au lieu de plusieurs Mo

### 3. **Intégration avec Vite**
Dans ce projet, **Vite** (outil Node.js) :
- Compile Tailwind + DaisyUI
- Optimise les assets
- Gère le rechargement à chaud
- Bundle les fichiers pour la production

---

## 📁 Configuration dans le projet

### Fichiers de configuration

**`tailwind.config.js`** - Configuration Tailwind
```javascript
module.exports = {
  content: [
    "./public/**/*.php",
    "./App/views/**/*.php",
    "./src/**/*.js"
  ],
  theme: {
    extend: {},
  },
  plugins: [require("daisyui")],
}
```

**`package.json`** - Dépendances Node.js
```json
{
  "devDependencies": {
    "tailwindcss": "^3.x.x",
    "daisyui": "^4.x.x",
    "vite": "^5.x.x"
  }
}
```

**`vite.config.ts`** - Configuration Vite
```typescript
import { defineConfig } from 'vite'

export default defineConfig({
  css: {
    postcss: {
      plugins: [
        require('tailwindcss'),
        require('autoprefixer'),
      ],
    },
  },
})
```

---

## 🎯 Exemples pratiques

### Exemple 1 : Carte avec DaisyUI
```html
<div class="card w-96 bg-base-100 shadow-xl">
  <figure><img src="image.jpg" alt="Photo" /></figure>
  <div class="card-body">
    <h2 class="card-title">Titre de la carte</h2>
    <p>Description de la carte</p>
    <div class="card-actions justify-end">
      <button class="btn btn-primary">Voir plus</button>
    </div>
  </div>
</div>
```

### Exemple 2 : Navigation
```html
<div class="navbar bg-base-100">
  <div class="navbar-start">
    <a class="btn btn-ghost text-xl">Mon Site</a>
  </div>
  <div class="navbar-center hidden lg:flex">
    <ul class="menu menu-horizontal px-1">
      <li><a>Accueil</a></li>
      <li><a>Activités</a></li>
      <li><a>Contact</a></li>
    </ul>
  </div>
  <div class="navbar-end">
    <a class="btn">Connexion</a>
  </div>
</div>
```

### Exemple 3 : Formulaire
```html
<div class="form-control w-full max-w-xs">
  <label class="label">
    <span class="label-text">Votre nom</span>
  </label>
  <input type="text" placeholder="Tapez ici" class="input input-bordered w-full max-w-xs" />
  <label class="label">
    <span class="label-text-alt">Aide ou erreur</span>
  </label>
</div>
```

---

## 🔄 Workflow de développement

### 1. **Installation des dépendances**
```bash
npm install
```

### 2. **Mode développement**
```bash
npm run dev
# Lance Vite en mode watch
# Recompile automatiquement à chaque changement
```

### 3. **Build de production**
```bash
npm run build
# Compile et optimise pour la production
# Purge le CSS inutilisé
# Minifie les fichiers
```

### 4. **Structure des fichiers**
```
src/
  main.css          # Point d'entrée Tailwind
public/assets/
  style.css         # CSS compilé (généré automatiquement)
```

---

## 📚 Ressources et documentation officielle

### Tailwind CSS
- **Site officiel** : [https://tailwindcss.com](https://tailwindcss.com)
- **Documentation** : [https://tailwindcss.com/docs](https://tailwindcss.com/docs)
- **Cheat Sheet** : [https://tailwindcomponents.com/cheatsheet](https://tailwindcomponents.com/cheatsheet)

### DaisyUI
- **Site officiel** : [https://daisyui.com](https://daisyui.com)
- **Composants** : [https://daisyui.com/components](https://daisyui.com/components)
- **Thèmes** : [https://daisyui.com/docs/themes](https://daisyui.com/docs/themes)

### Vite
- **Documentation** : [https://vitejs.dev](https://vitejs.dev)
- **Guide CSS** : [https://vitejs.dev/guide/features.html#css](https://vitejs.dev/guide/features.html#css)

---

## 🎨 Thèmes DaisyUI

DaisyUI propose plusieurs thèmes prêts à l'emploi :

```html
<!-- Changer de thème -->
<html data-theme="light">    <!-- Thème clair -->
<html data-theme="dark">     <!-- Thème sombre -->
<html data-theme="cupcake">  <!-- Thème pastel -->
<html data-theme="corporate"><!-- Thème professionnel -->
```

### Variables CSS automatiques
```css
/* DaisyUI génère automatiquement ces variables */
.btn-primary {
  background-color: var(--p);    /* Couleur primaire du thème */
  color: var(--pc);              /* Couleur du texte primaire */
}
```

---


## 🚀 Commandes utiles

```bash
# Installation
npm install

# Développement
npm run dev

# Production
npm run build

# Vérifier les dépendances
npm outdated

# Mettre à jour
npm update
```

Cette approche moderne permet un développement frontend efficace tout en gardant un code maintenable et performant.
