Pour mesurer la **similarité** $\mathrm{sim}(s,s')$ entre deux sessions $s$ et $s'$ en s’appuyant uniquement sur les **attributs métier** disponibles dans votre schéma, on procède en deux étapes :

---

### 1. Construction du vecteur de caractéristiques d’une session

Pour chaque session $s$, on extrait via SQL/join les éléments suivants :

| Dimension              | Table(s) et champ(s)                         | Encodage                                     |
| ---------------------- | -------------------------------------------- | -------------------------------------------- |
| **Jeu**                | `sessions.id_activite` → `activites.id_jeu`  | one-hot par `jeux.id`                        |
| **Genre(s)**           | `activites.id_jeu` → `jeux_genres.id_genre`  | one-hot par `genres.id`                      |
| **Type d’activité**    | `activites.type_activite`                    | one-hot (CAMPAGNE, ONESHOT, …)               |
| **Type de campagne**   | `activites.type_campagne`                    | one-hot (OUVERTE, FERMEE, NULL→zéro)         |
| **Lieu**               | `sessions.id_lieu`                           | one-hot par `lieux.id`                       |
| **Jour de la semaine** | `sessions.date_session` → `DAYOFWEEK(date)`  | one-hot (1=Dimanche … 7=Samedi)              |
| **Créneau horaire**    | `sessions.heure_debut` → `HOUR(heure_debut)` | bucket (ex. Matin, Après-midi, Soir) one-hot |

> **Remarque :**
>
> * On récupère `id_jeu`, `type_activite`, `type_campagne` de la table **activites** liée à `sessions`.
> * Les genres se font via la table d’association **jeux\_genres**.
> * Les buckets horaires peuvent être, par exemple :
>
>   * Matin (6–12h), Après-midi (12–18h), Soir (18–23h).

On en déduit un vecteur binaire/creux

$$
\mathbf{f}_s = \bigl(f_{s,d}\bigr)_{d\in\mathcal{D}},
\quad f_{s,d}\in\{0,1\},
$$

où $\mathcal{D}$ est l’union des modalités ci-dessus (tous jeux, tous genres, etc.).

---

### 2. Mesure de similarité

Deux mesures classiques :

1. **Cosinus**

   $$
   \mathrm{sim}_{\cos}(s,s')
   = \frac{\mathbf{f}_s \cdot \mathbf{f}_{s'}}
          {\|\mathbf{f}_s\|_2 \;\|\mathbf{f}_{s'}\|_2}
   = \frac{\sum_{d} f_{s,d}\,f_{s',d}}
          {\sqrt{\sum_{d} f_{s,d}^2}\;\sqrt{\sum_{d} f_{s',d}^2}}.
   $$

   – Favorise les sessions qui partagent **proportionnellement** beaucoup de caractéristiques.
   – Valeur dans $[0,1]$.

2. **Jaccard**

   $$
   \mathrm{sim}_{\rm J}(s,s')
   = \frac{|\{d:f_{s,d}=1\}\,\cap\,\{d:f_{s',d}=1\}|}
          {|\{d:f_{s,d}=1\}\,\cup\,\{d:f_{s',d}=1\}|}
   = \frac{\sum_d \min(f_{s,d},f_{s',d})}
          {\sum_d \max(f_{s,d},f_{s',d})}.
   $$

   – Mesure la **proportion de chevauchement** dans l’union des attributs.
   – Particulièrement adaptée aux vecteurs très creux.

---

### 3. Implémentation SQL / PHP

#### a. Extraction des features en SQL

```sql
WITH session_meta AS (
  SELECT
    s.id                                  AS session_id,
    a.id_jeu                              AS jeu_id,
    a.type_activite                       AS type_activite,
    COALESCE(a.type_campagne,'NONE')      AS type_campagne,
    s.id_lieu                             AS lieu_id,
    DAYOFWEEK(s.date_session)             AS jour_sem,   -- 1=Dim…7=Sam
    HOUR(s.heure_debut)                   AS heure
  FROM sessions s
  JOIN activites a ON s.id_activite = a.id
)
SELECT
  sm.session_id,
  sm.jeu_id,
  jg.id_genre,
  sm.type_activite,
  sm.type_campagne,
  sm.lieu_id,
  sm.jour_sem,
  CASE
    WHEN sm.heure BETWEEN 6  AND 11 THEN 'MATIN'
    WHEN sm.heure BETWEEN 12 AND 17 THEN 'APREM'
    ELSE 'SOIR'
  END AS creneau
FROM session_meta sm
LEFT JOIN jeux_genres jg ON sm.jeu_id = jg.id_jeu;
```

> Ce résultat ligne-par-ligne permet de construire en PHP un tableau associatif
> `features[s][dimension] = 1`.

#### b. Calcul de $\mathrm{sim}(s,s')$ en PHP

```php
function cosine_sim($f_s, $f_sp) {
    $dot = 0; $norm_s = 0; $norm_sp = 0;
    foreach ($f_s as $d => $v) {
        if ($v) {
            $norm_s++;
            if (!empty($f_sp[$d])) {
                $dot++;
            }
        }
    }
    foreach ($f_sp as $v) {
        if ($v) { $norm_sp++; }
    }
    return $dot / (sqrt($norm_s) * sqrt($norm_sp));
}

function jaccard_sim($f_s, $f_sp) {
    $inter = 0; $union = 0;
    $all_keys = array_unique(array_merge(array_keys(array_filter($f_s)),
                                         array_keys(array_filter($f_sp))));
    foreach ($all_keys as $d) {
        $vs = !empty($f_s[$d]) ? 1 : 0;
        $vsp= !empty($f_sp[$d])? 1 : 0;
        $inter += min($vs,$vsp);
        $union += max($vs,$vsp);
    }
    return $union ? $inter/$union : 0;
}
```

---

### 4. Choix de la mesure

* **Cosinus** si vous voulez valoriser le **poids relatif** (éviter de trop favoriser les sessions avec un grand nombre d’attributs).
* **Jaccard** si vos vecteurs sont très creux et que vous cherchez un ratio d’intersection sur l’union.

Vous pouvez même combiner :

$$
\mathrm{sim}(s,s')
= \gamma\,\mathrm{sim}_{\cos}(s,s')
+ (1-\gamma)\,\mathrm{sim}_{\rm J}(s,s'),
$$

pour ajuster au cas d’usage.

---

**Résumé**

1. On extrait, via `sessions` → `activites` → `jeux_genres` → `lieux`, un vecteur binaire de dimensions métiers.
2. On calcule la similarité soit en **cosinus**, soit en **Jaccard**, selon la granularité et la sparsité des données.
3. Ce $\mathrm{sim}(s,s')$ sert ensuite dans l’étape MMR pour favoriser la diversité entre sessions recommandées.
