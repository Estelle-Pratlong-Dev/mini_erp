# Procédure de mise à jour — mini_erp (IONOS mutualisé, SFTP + phpMyAdmin)

> Contexte : pas de SSH en ligne. On modifie/teste en local (Laragon), puis on **envoie
> uniquement les fichiers modifiés** et on **applique à la main** les éventuels changements
> de base de données. On ne réimporte **jamais** `database.sql` (ce fichier n'est que
> l'installation initiale : il écraserait tes données de production).

---

## ⚠️ Règles d'or (à ne jamais oublier)

1. **Sauvegarder AVANT toute mise à jour** (voir §1). Une MAJ ratée sans sauvegarde = données perdues.
2. Ne **jamais écraser** sur le serveur :
   - **`.env.local`** → c'est ta config de prod (base, domaine, secret).
   - **`var/uploads/`** → ce sont tes fichiers (photos de projets, pièces jointes).
   - Les **données** de la base → on n'applique que les *changements de structure* (voir §4).
3. Ne jamais réimporter `database.sql`.
4. Ne jamais laisser `APP_DEBUG=1` en ligne.

---

## 1. Sauvegarder (obligatoire)

- **Base de données** : phpMyAdmin → sélectionne ta base → **Exporter** → format SQL → enregistre le fichier (nomme-le avec la date, ex. `sauvegarde_2026-08-27.sql`).
- **Fichiers uploadés** : par SFTP, télécharge le dossier **`var/uploads/`** en entier.

Garde ces deux sauvegardes : c'est ton filet de sécurité pour revenir en arrière (§6).

---

## 2. Côté local (Laragon) : préparer la nouvelle version

Après avoir codé et testé, dans le dossier du projet (Git Bash) :

```bash
# 1) Vérifier que tout est commité
git status

# 2) Réinstaller les dépendances SANS dev si composer.json/lock a changé
composer install --no-dev --optimize-autoloader --no-scripts

# 3) Recompiler les assets si tu as touché à assets/ ou aux templates JS/CSS
php bin/console asset-map:compile

# 4) Voir la liste des fichiers modifiés depuis la dernière mise en ligne
#    (nécessite d'avoir taggé la version précédente, voir l'astuce plus bas)
git diff --name-only DERNIER_TAG HEAD
```

> **Astuce “tag de version”** : à chaque mise en ligne réussie, marque le point :
> ```bash
> git tag deploy-2026-08-27 && git push --tags
> ```
> La fois suivante, `git diff --name-only deploy-2026-08-27 HEAD` te donne **exactement**
> la liste des fichiers à renvoyer. Sans tag, dans le doute, renvoie les dossiers
> `src/`, `templates/`, `config/`, `translations/`, et `public/assets/` + `migrations/`.

---

## 3. Envoyer les fichiers modifiés (SFTP)

Envoie **seulement** les fichiers/dossiers modifiés, par-dessus les anciens :

- Code : `src/`, `templates/`, `config/`, `translations/`
- Si dépendances changées : `vendor/` (tout le dossier) + `composer.json` + `composer.lock`
- Si assets changés : `public/assets/` (et `public/bundles/` si une lib a changé)
- Si base modifiée : les nouveaux fichiers de `migrations/` (pour référence)

**Ne touche pas** à `.env.local` ni à `var/uploads/`.

---

## 4. Appliquer les changements de base de données (s'il y en a)

Une MAJ ne change la base **que si** de **nouveaux fichiers sont apparus dans `migrations/`**
depuis la dernière fois. Si aucun nouveau fichier de migration → **rien à faire ici**, passe au §5.

S'il y a de nouvelles migrations, deux options :

### Option A — je te fournis un `maj.sql` (recommandé)
Demande-moi le script : je te livre un fichier `maj.sql` contenant **exactement** les
commandes à jouer. Dans phpMyAdmin → ta base → onglet **SQL** → colle le contenu → **Exécuter**.

### Option B — le faire toi-même depuis les fichiers de migration
1. Ouvre chaque **nouveau** fichier `migrations/VersionXXXXXX.php`.
2. Dans la méthode `up()`, recopie le contenu de **chaque** `$this->addSql('...')`
   (juste la partie SQL entre les quotes).
3. Colle ces requêtes dans phpMyAdmin (onglet **SQL**) → **Exécuter**, dans l'ordre des fichiers.

> Ces requêtes sont des `ALTER TABLE` / `CREATE TABLE` : elles **ajoutent** des colonnes/tables
> sans toucher à tes données existantes.

---

## 5. Vider le cache et vérifier

1. Sur le serveur (SFTP), **supprime tout le contenu** du dossier **`var/cache/`**
   (laisse le dossier `var/cache/` lui-même). Symfony le régénère au prochain accès.
2. Recharge le site → vérifie que tout fonctionne (connexion, une page de chaque module modifié).
3. Marque la version déployée en local : `git tag deploy-AAAA-MM-JJ && git push --tags`.

---

## 6. En cas de problème (revenir en arrière)

1. **Fichiers** : renvoie la version précédente des fichiers (ou restaure depuis Git :
   `git checkout DERNIER_TAG -- <fichiers>` puis ré-upload).
2. **Base** : si une migration a cassé quelque chose, réimporte ta **sauvegarde SQL** du §1
   (phpMyAdmin → Importer). ⚠️ cela ramène la base à l'état de la sauvegarde (tu perds les
   données saisies entre-temps — d'où l'importance de sauvegarder juste avant la MAJ).
3. **Uploads** : restaure le dossier `var/uploads/` sauvegardé si besoin.
4. Vide `var/cache/`.

---

## Récapitulatif express

```
1. Sauvegarder (BDD via phpMyAdmin + var/uploads via SFTP)
2. En local : composer install --no-dev / asset-map:compile / git diff --name-only <tag> HEAD
3. Envoyer les fichiers modifiés (PAS .env.local, PAS var/uploads)
4. Si nouvelles migrations : jouer maj.sql (ou les addSql) dans phpMyAdmin
5. Vider var/cache/ → recharger → vérifier → git tag deploy-<date>
```
