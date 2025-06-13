Pour chaque utilisateur $u$ on définit un **vecteur d’empreinte**

$$
\mathbf{p}_u \;=\;\bigl(p_{u,d}\bigr)_{d\in\mathcal{D}}
$$

où $\mathcal{D}$ est l’ensemble de **toutes** les dimensions discrètes :

* les **jeux** (chaque titre),
* les **genres** (et sous-genres),
* les **types d’activité** (campagne ouverte/fermée, one-shot, plateau, événement…),
* les **jours de semaine** (Lun, Mar, …, Dim),
* les **créneaux horaires** (ex. mat’, aprem’, soir, ou par tranche d’heure),
* les **lieux** (salle A, B, …),
* les **“amis”** (autres utilisateurs co-inscrits).

---

### 1. Décroissance temporelle

À chaque historique d’activité $i\in H_u$ on associe

$$
w_{t_i}\;=\;\exp\Bigl(-\lambda\,(T - t_i)\Bigr)
\quad\text{avec}\quad
\lambda = \frac{\ln 2}{v}
$$

où

* $T$ est la date « aujourd’hui »,
* $t_i$ la date de l’activité $i$,
* $v$ la demi-vie choisie (ex. : 30 j pour du one-shot, 90 j pour des campagnes).

---

### 2. Formule générale

Pour chaque dimension $d\in\mathcal{D}$, on calcule

$$
p_{u,d}
\;=\;
\sum_{i\in H_u}
w_{t_i}\;\times\;
\underbrace{\mathbf{1}_{\{\text{act}_i\text{ possède }d\}}}_{%
  =1\text{ si }d\text{ est présent dans l’activité }i,\;0\text{ sinon}}
$$

---

### 3. Détail par dimension

1. **Jeu $j$**

   $$
     p_{u,\text{jeu}=j}
     = \sum_{i\in H_u} w_{t_i}\,\mathbf{1}_{\{\text{jeu}(i)=j\}}
   $$
2. **Genre $g$**

   $$
     p_{u,\text{genre}=g}
     = \sum_{i\in H_u} w_{t_i}\,\mathbf{1}_{\{\text{genre}(i)=g\}}
   $$
3. **Type d’activité $t$** (campagne ouverte, one-shot, …)

   $$
     p_{u,\text{type}=t}
     = \sum_{i\in H_u} w_{t_i}\,\mathbf{1}_{\{\text{type}(i)=t\}}
   $$
4. **Jour de la semaine $D\in\{\mathrm{Lun},…,\mathrm{Dim}\}$**

   $$
     p_{u,D}
     = \sum_{i\in H_u} w_{t_i}\,\mathbf{1}_{\{\mathrm{jour}(t_i)=D\}}
   $$
5. **Créneau horaire $H$** (ex. 18–20h)

   $$
     p_{u,H}
     = \sum_{i\in H_u} w_{t_i}\,\mathbf{1}_{\{\mathrm{heure}(t_i)\in H\}}
   $$
6. **Lieu $\ell$**

   $$
     p_{u,\ell}
     = \sum_{i\in H_u} w_{t_i}\,\mathbf{1}_{\{\text{lieu}(i)=\ell\}}
   $$
7. **“Amitié” / co-inscription avec un autre utilisateur $v$**

   $$
     p_{u,v}
     = \sum_{i\in H_u} w_{t_i}\,\mathbf{1}_{\{v\in U_i\}}
   \quad\bigl(U_i=\text{ensemble des inscrits à }i\bigr)
   $$

---

### 4. Normalisation et importance

1. **Variance**
   Calculez, sur l’ensemble des utilisateurs,
   $\sigma_d^2 = \mathrm{Var}\bigl(p_{u,d}\bigr)$.
2. **Poids d’influence**

   $$
     \alpha_d
     = \frac{1/\sigma_d^2}{\sum_{d'}1/\sigma_{d'}^2}
     \quad\text{et}\quad\sum_d\alpha_d=1.
   $$
3. **Vecteur final normalisé**
   Vous pouvez normaliser $\mathbf{p}_u$ par la somme des poids si besoin, ou l’utiliser directement pour scorer :

   $$
     S_{CB}(u,s)
     = \sum_d \alpha_d\;\bigl[p_{u,d}\times\mathbf{1}_{\{s\text{ possède }d\}}\bigr].
   $$

---

> **Résumé** :
>
> * on agrège **temps + attribut** via $w_{t_i}\times\mathbf{1}_{\{\dots\}}$,
> * on évalue la **stabilité** de chaque dimension par sa variance $\sigma_d^2$,
> * puis on pondère chaque dimension par $\alpha_d\propto1/\sigma_d^2$.
>
> En PHP, il suffit de stocker en tables les $p_{u,d}$ et $\alpha_d$ calculés en batch, puis de faire un **dot-product** pour générer vos recommandations.

Voici la **formule complète** en une seule passe, qui intègre :

1. **Fallback popularité**
2. **Content-based**
3. **Dynamique cold-start** via $\delta_u$
4. **Diversification** par **MMR**

---

### 1. Définitions préalables

* $\displaystyle S_{CB}(u,s)$ : **content-based** comme vu précédemment.
* $\displaystyle S_{\rm pop}(s)  = \frac{\log\bigl(1+\mathrm{inscrits}(s)\bigr)}%
          {\max_{s'}\log\bigl(1+\mathrm{inscrits}(s')\bigr)}$.
* $\displaystyle \delta_u = \exp\!\bigl(-\,|H_u|/\tau\bigr)$,
  où $|H_u|$ = nombre de sessions passées de $u$,
  $\tau$ = paramètre de « bascule » (ex. 5–10).
* $\displaystyle S_{\rm comb}(u,s)  = (1 - \delta_u)\,S_{CB}(u,s)\;+\;\delta_u\,S_{\rm pop}(s)$.
* $\displaystyle \mathrm{sim}(s,s')$ : similarité entre sessions (p.ex. cosinus ou Jaccard sur leurs dimensions).

---

### 2. Score mixte

Pour chaque session candidate $s$ on calcule d’abord :

$$
  S_{\rm comb}(u,s)
  = (1 - \delta_u)\,S_{CB}(u,s)
    + \delta_u\,S_{\rm pop}(s)
  \quad\in[0,1].
$$

---

### 3. Sélection via Maximum Marginal Relevance (MMR)

On veut sélectionner **itérativement** un ensemble de $K$ sessions $R=\{s_1,\dots,s_K\}$ qui maximise à la fois la pertinence ($S_{\rm comb}$) et la diversité :

1. Choisir un paramètre $\lambda_{\rm mmr}\in[0,1]$ réglant le compromis « pertinence vs diversité ».
2. Initialiser $R_0=\varnothing$.
3. Pour $i=1$ à $K$ :

   $$
     s_i 
     = \underset{s\in C \setminus R_{i-1}}{\arg\max}\;
     \Bigl[
       \underbrace{\lambda_{\rm mmr}\,\;S_{\rm comb}(u,s)}_{\text{pertinence}}
       \;-\;
       \underbrace{(1-\lambda_{\rm mmr})\,\max_{r\in R_{i-1}}\mathrm{sim}(s,r)}_{\text{diversité}}
     \Bigr].
   $$
4. Retourner $R_K$.

* Quand $\lambda_{\rm mmr}=1$, on ne fait que du pure score (aucune diversité).
* Quand $\lambda_{\rm mmr}=0$, on maximise la diversité pure.

---

### 4. Récapitulatif de la pipeline

1. **Offline**

   * Calcul des $S_{CB}(u,s)$ et des scores $\mathrm{inscrits}(s)$ pour tous $u,s$.
   * Stockage de $\max_{s'}\log(1+\mathrm{inscrits}(s'))$ et des vecteurs de profil.
2. **Online** (PHP)

   * Charger $\delta_u$ (fonction de $|H_u|$), $S_{CB}(u,s)$ et $S_{\rm pop}(s)$.
   * Pour chaque candidat $s$, calculer
     $\;S_{\rm comb}(u,s)$.
   * Appliquer l’algorithme MMR pour extraire les top-$K$ diversifiées.

---

Avec cette méthode, tu conjugues :

* **Cold-start** ► grâce à $\delta_u\,S_{\rm pop}$,
* **Personnalisation** ► via $(1-\delta_u)\,S_{CB}$,
* **Découverte & diversité** ► via MMR.
