Voici la présentation **complète** de la formule de recommandation, intégrant :

1. **Décroissance temporelle** (avec demi-vie potentiellement dynamique)
2. **Content-based**  $S_{CB}$
3. **Fallback popularité**  $S_{\rm pop}$
4. **Cold-start dynamique** via le poids $\delta_u$
5. **Sélection diversifiée** par **MMR**

---

## 1. Décroissance temporelle

1. On calcule d’abord, pour chaque utilisateur $u$ et chaque session passée $i\in H_u$ (jouée à la date $t_i$), un poids

   $$
     w_{t_i}
     = \exp\!\bigl(-\lambda_u\,(T - t_i)\bigr),
     \quad
     \lambda_u = \frac{\ln2}{v_u}.
   $$
2. La **demi-vie** $v_u$ peut être **dynamique**, p. ex.

   $$
     v_u
     = 60\,\%\_{\rm campagne}
       + 30\,\%\_{\rm one\text{-}shot}
       + 100\,\%\_{\rm event}
       + 100\,\%\_{\rm plateau},
   $$

   où les pourcentages sont la proportion de chaque type dans l’historique de $u$.

---

## 2. Score content-based $S_{CB}(u,s)$

Pour chaque dimension $d$ (jeu, genre, jour, créneau, lieu, co-inscription…) :

1. Empreinte utilisateur

   $$
     p_{u,d}
     = \sum_{i\in H_u} w_{t_i}\;\mathbf{1}_{\{\text{act}_i\text{ possède }d\}}.
   $$
2. Poids d’influence $\alpha_d$ par variance

   $$
     \sigma_d^2 = \mathrm{Var}_{u}\bigl(p_{u,d}\bigr),
     \quad
     \alpha_d = \frac{1/\sigma_d^2}{\sum_{d'}1/\sigma_{d'}^2},
     \quad\sum_d\alpha_d=1.
   $$
3. Score d’une session candidate $s$

   $$
     S_{CB}(u,s)
     = \sum_{d}\alpha_d\;\bigl[p_{u,d}\,\mathbf{1}_{\{s\text{ a }d\}}\bigr].
   $$

---

## 3. Score popularité $S_{\rm pop}(s)$

$$
  S_{\rm pop}(s)
  = \frac{\log\bigl(1 + \mathrm{inscrits}(s)\bigr)}
         {\max_{s'}\log\bigl(1 + \mathrm{inscrits}(s')\bigr)},
  \quad
  S_{\rm pop}\in[0,1].
$$

---

## 4. Poids Cold-start $\delta_u$

$$
  \delta_u
  = \exp\!\Bigl(-\frac{|H_u|}{\tau}\Bigr),
  \quad
  \tau\approx5\text{–}10,
  \quad
  \delta_u\in[0,1].
$$

* Si $|H_u|=0$ alors $\delta_u=1$ (100 % popularité).
* Dès $|H_u|\approx\tau$, $\delta_u$ chute vers $\approx37\%$.

---

## 5. Score combiné

$$
  S_{\rm comb}(u,s)
  = (1-\delta_u)\,S_{CB}(u,s)
    \;+\;\delta_u\,S_{\rm pop}(s).
$$

---

## 6. Sélection diversifiée par MMR

Soit $C$ l’ensemble des sessions candidates et $K$ le nombre de recommandations :

1. Choisir $\lambda_{\rm mmr}\in[0,1]$.
2. Initialiser $R_0=\varnothing$.
3. Pour $i=1$ à $K$ :

   $$
     s_i
     = \underset{s\in C\setminus R_{i-1}}{\arg\max}\;\Bigl[
       \lambda_{\rm mmr}\,S_{\rm comb}(u,s)
       \;-\;(1-\lambda_{\rm mmr})\,\max_{r\in R_{i-1}}\mathrm{sim}(s,r)
     \Bigr].
   $$
4. {\small où $\mathrm{sim}(s,r)$ est une similarité (p.ex. cosinus ou Jaccard) entre sessions.}
5. Retourner $\{s_1,\dots,s_K\}$.

* $\lambda_{\rm mmr}=1$ → pure pertinence,
* $\lambda_{\rm mmr}=0$ → pure diversité,
* Valeurs intermédiaires équilibrent les deux.

---

### Pipeline de déploiement

1. **Offline** (cron/Python)

   * Calculs de $w_{t_i}$, $p_{u,d}$, $\alpha_d$, $\max\log(1+\mathrm{inscrits})$.
   * Stockage en base : profils $p_{u,*}$, poids $\alpha_d$, scores popularité.
2. **Online** (PHP)

   * Charger    , vecteurs $p_{u,*}$, $\alpha_d$ et $S_{\rm pop}$.
   * Calculer $S_{\rm comb}(u,s)$ pour chaque $s$.
   * Appliquer l’algorithme MMR pour extraire les $K$ recommandations.

---

Avec cette **formule unique**, ton système :

* démarre bien pour les nouveaux (fallback popularité),
* grandit vers de la personnalisation forte (CB),
* reste à jour dans le temps (décroissance + demi-vie dynamique),
* propose de la diversité réelle (MMR).
