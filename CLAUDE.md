# CLAUDE.md

Guidance pour Claude Code lorsqu'il travaille sur ce dépôt.

## Présentation

**mini_erp** : ERP modulaire pour petites entreprises (TPE), destiné à être **commercialisé**.
Symfony 8 + Doctrine + MySQL. EasyAdmin 5.1 pour le back-office d'administration ;
une « appli de base » en Twig + Bootstrap 5 (CDN) pour l'usage quotidien.

Chaîne métier cible : **Projet → Contrat → Facture**, plus Vente directe (caisse), Stock,
Dépenses, Statistiques, Compta. Voir le plan complet dans la conversation d'origine.

## Commandes

```bash
symfony server:start --no-tls --port=8000     # serveur de dev
php bin/console cache:clear
php bin/console make:migration                 # générer une migration depuis les entités
php bin/console doctrine:migrations:migrate    # appliquer
php bin/console app:install                    # (idempotent) seed modules/permissions/admin/société

# Tests
php bin/phpunit                                # toute la suite
php bin/phpunit tests/Unit                     # tests unitaires uniquement
# Base de test (à créer une fois) :
php bin/console --env=test doctrine:database:create --if-not-exists
php bin/console --env=test doctrine:migrations:migrate --no-interaction
```

## Tests
- **PHPUnit** (symfony/test-pack). Base de test : `mini_erp_test` (suffixe `_test` ajouté par
  `doctrine` en env test ; `DATABASE_URL` dans `.env.test`).
- `tests/Unit/` : tests purs sans base (totaux Contrat/LigneArticle, RBAC `User::getRoles`).
- `tests/Functional/` : `KernelTestCase` avec base, isolés par transaction annulée
  (filtre soft-delete, validations métier).

Base : MySQL Laragon (`root` sans mot de passe), configurée dans `.env.local`.
Client mysql : `C:/laragon/bin/mysql/mysql-8.4.3-winx64/bin/mysql.exe -uroot mini_erp`.

Admin par défaut (créé par `app:install`) : `admin@mini-erp.local` / `admin`.

## Architecture

### Deux interfaces
- **Back-office admin** EasyAdmin sur `/admin`, réservé `ROLE_ADMIN`
  (`src/Controller/Admin/`). Gère : Société, Modules, Utilisateurs, Rôles, Permissions.
- **Appli de base** (reste des routes), réservée `ROLE_USER` : `src/Controller/*Controller.php`
  + templates `templates/app/layout.html.twig` (sidebar). Contacts, Catalogue, …

### Modules activables
- Entité `Module` (enum `App\Enum\CodeModule`) + service `App\Service\ModuleManager`
  (`isEnabled()`, cache par requête).
- Un contrôleur d'appli de base déclare son module via l'attribut
  `#[App\Attribute\RequireModule(CodeModule::XXX)]`. Le `ModuleAccessSubscriber` bloque
  l'accès (redirection vers `app_home`) si le module est désactivé.
- Menu : la fonction Twig `module_actif('CODE')` (voir `App\Twig\AppExtension`) masque/affiche
  les entrées dans `templates/app/layout.html.twig`.

### RBAC piloté par la base
- Entités `Role` et `Permission` (M2M `role_permission`), `User` ↔ `Role` (M2M `user_role`).
- Permissions générées par module : `<CODE_MODULE>_<ACTION>` avec ACTION ∈
  VOIR/CREER/MODIFIER/SUPPRIMER (ex. `CONTACTS_VOIR`, `CATALOGUE_CREER`).
- `User::getRoles()` renvoie `ROLE_USER` + `ROLE_<code rôle>` + `ROLE_<code permission>`
  (+ `ROLE_ADMIN` si un rôle a le code `ADMIN`). On vérifie donc avec
  `#[IsGranted('ROLE_CONTACTS_VOIR')]` dans les contrôleurs et `is_granted(...)` dans Twig.
- Le rôle `ADMIN` reçoit **toutes** les permissions (re-synchronisées par `app:install`).

### Suppression logique (soft delete)
- Les entités supprimables implémentent `App\Entity\SoftDeletableInterface` et utilisent
  `App\Trait\SoftDeleteTrait` (**booléen `supprime`**). Concernées : User, Role, Permission,
  Contact, Produit, Projet, Contrat, Facture, PieceJointe.
- La **date et l'auteur** de la suppression ne sont pas stockés à part : une suppression est une
  modification, donc `modifieLe` / `modifiePar` (audit) sont mis à jour automatiquement au flush.
- Le filtre Doctrine `App\Doctrine\SoftDeleteFilter` (activé dans `doctrine.yaml`) exclut
  **automatiquement** les éléments supprimés (`supprime = 1`) de toutes les requêtes
  (listes, find, associations, count).
- On ne fait jamais `$em->remove()` sur ces entités : on appelle `setSupprime(true)`.
  Côté EasyAdmin, `SoftDeleteCrudTrait::deleteEntity()` fait de même.
- Conséquence connue : un enregistrement supprimé conserve sa valeur d'unicité (ex. email,
  référence produit) — à gérer si on veut réutiliser ces valeurs.

### Audit
- `App\Trait\TimestampableTrait` : champs **en français** `creeLe` / `creePar` / `modifieLe` /
  `modifiePar` (colonnes `cree_le`, `cree_par_id`, `modifie_le`, `modifie_par_id`).
- Présent sur **toutes les entités** (Contact, Produit, Projet, Contrat, Facture, LigneArticle,
  PieceJointe, User, Societe, Module, Role, Permission).
- Remplissage **automatique** par `App\EventListener\TimestampableListener`
  (Doctrine prePersist/preUpdate). Pas besoin de le faire dans les contrôleurs.

### API REST (API Platform)
- Exposition **déclarative** via l'attribut `#[ApiResource]` **sur l'entité** (pas de contrôleur).
- Entités exposées : **Contact** (`/api/contacts`), **Produit** (`/api/produits`),
  **Projet** (`/api/projets`), **Contrat** (`/api/contrats`), **Facture** (`/api/factures`) —
  Contrat et Facture avec leurs **lignes imbriquées** (lecture/écriture) + totaux calculés.
  Chaque ressource a GET liste/détail, POST, PATCH, DELETE. La création de facture via l'API
  passe par `FacturePersistProcessor` (attribution du numéro).
- Relations exposées en **IRI** (ex. `projet: "/api/projets/4"`) ; formats `jsonld` et `json`.
- **Groupes de sérialisation** (`#[Groups(['contact:read'/'contact:write'])]`) pour ne pas exposer
  l'audit ni `supprime`.
- **Sécurité par opération** réutilisant le RBAC : `security: "is_granted('ROLE_CONTACTS_VOIR')"` etc.
- **DELETE = soft delete** via `App\State\SoftDeleteProcessor` (cohérent avec le reste de l'app).
- Le filtre soft-delete et le listener d'audit s'appliquent **automatiquement** aussi à l'API.
- Doc interactive : `/api/docs`. Config : `config/packages/api_platform.yaml`
  (actuellement `stateless: false` → réutilise l'auth par **session** ; pour une vraie API,
  prévoir un **firewall `/api` stateless + JWT**).
- Pour exposer une autre entité : ajouter le même `#[ApiResource]` + `#[Groups]` dessus.

### Convention de nommage
- **Tout le schéma est en français** (propriétés et colonnes). Seuls restent en anglais des
  champs techniques standard de Symfony sur `User` : `email`, `password`, `roles`.

## Conventions

- **PHP 8.4+**, promotion de propriétés, enums typés (`enumType:` en colonne, VARCHAR).
- **CSRF stateful** (basé session, voir `config/packages/csrf.yaml`) — pas de dépendance JS.
- Montants en `decimal` (string côté PHP) ; UI/formulaires en français.
- EasyAdmin 5.1 : menu via `MenuItem::linkTo(XxxCrudController::class, label, icon)` ;
  panneaux via `FormField::addFieldset()` (pas `addPanel`).

## Pièces jointes (Phase 2)
- Entité `PieceJointe` rattachable à un `Projet`, un `Contrat` et/ou une `Facture`.
- Fichiers stockés hors web dans `var/uploads/pieces/` (gitignoré via `var/`), servis par
  `PieceJointeController` (download sécurisé `ROLE_USER`). Upload manuel (pas de VichUploader).

## Lignes de documents
- `LigneArticle` (table `ligne_article`) = les **lignes d'articles** d'un document commercial.
  Partagée entre `Contrat` et `Facture` (un seul parent renseigné par ligne).
  À ne pas confondre avec `PieceJointe` (fichiers joints) ni le PDF imprimable (généré à la volée).
- Saisie dynamique des lignes via `CollectionType` + JS vanilla (prototype) dans
  les formulaires contrat/facture — pas de build front.

## Facturation (Phase 3)
- `Facture` rattachée à un `Projet` (jamais orpheline), `Contact` optionnel, `Contrat` d'origine opt.
- **Avoir = facture à montant négatif** (`isAvoir()` / `getLibelleType()`), pas de type distinct.
- **Numérotation séquentielle sûre** : `App\Service\FactureNumeroGenerator` (verrou pessimiste sur
  `Societe`, format `prefixeFacture` + n° sur 5 chiffres). Attribuée à la création (UI et API via
  `FacturePersistProcessor`).
- **Délai de paiement** (`DelaiPaiement`) → l'**échéance est calculée** (`appliquerDelaiPaiement()`),
  et **mode de paiement** (`ModePaiement`).
- **Conversion contrat → facture** : action sur la fiche contrat (copie les lignes, lie chaque ligne
  à sa ligne de contrat via `LigneArticle.ligneSource`).
- **Facturation partielle** (facture issue d'un contrat) : PU **figé**, et par ligne 3 colonnes
  synchronisées (quantité / montant / % facturés) recalculées entre elles côté JS ; la quantité
  facturée est la valeur stockée, le montant = quantité × PU, le % = quantité / quantité contrat.
  Champ **« % global »** pour appliquer un pourcentage à toutes les lignes d'un coup.
- **Facturation à l'avancement** : `montantDejaFactureHt/Tva` (snapshot figé à la création =
  somme des factures précédentes du contrat) → totaux **nets** (`getNetHt/Tva/Ttc`) affichés avec
  une ligne de déduction. **Seule la dernière facture du contrat est modifiable** (lignes verrouillées
  sinon) — voir `FactureRepository::estDerniere()` + option `lignes_modifiables` du `FactureType`.
- **Délai de paiement** (`DelaiPaiement` : à réception / à la commande / 15-30-45-60 j) calcule
  l'échéance (champ échéance conservé, éditable).
- **PDF** (facture et devis/contrat) via `App\Service\PdfGenerator` (façade Dompdf) +
  templates `templates/pdf/{facture,contrat}.html.twig` (en-tête Société).

## État (phases livrées)
- **Phase 0** : socle (auth, Société, Modules, RBAC, EasyAdmin admin).
- **Phase 1** : Contacts, Catalogue (Produits).
- **Phase 2** : Projets, Contrats/Devis (lignes + totaux), Pièces jointes.
- **Phase 3** : Facturation (numérotation auto, avoir, conversion contrat→facture, PDF), API + tests.
- À venir : Vente directe, Stock+Commandes, Dépenses+Statistiques, Compta/Factur-X.
