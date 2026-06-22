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
```

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

### Audit
- `App\Trait\TimestampableTrait` (`createdAt/By`, `updatedAt/By`) sur les entités métier.
- Remplissage **automatique** par `App\EventListener\TimestampableListener`
  (Doctrine prePersist/preUpdate). Pas besoin de le faire dans les contrôleurs.

## Conventions

- **PHP 8.4+**, promotion de propriétés, enums typés (`enumType:` en colonne, VARCHAR).
- **CSRF stateful** (basé session, voir `config/packages/csrf.yaml`) — pas de dépendance JS.
- Montants en `decimal` (string côté PHP) ; UI/formulaires en français.
- EasyAdmin 5.1 : menu via `MenuItem::linkTo(XxxCrudController::class, label, icon)` ;
  panneaux via `FormField::addFieldset()` (pas `addPanel`).

## État (phases livrées)
- **Phase 0** : socle (auth, Société, Modules, RBAC, EasyAdmin admin).
- **Phase 1** : Contacts, Catalogue (Produits) dans l'appli de base.
- À venir : Projets/Contrats, Facturation (PDF), Vente directe, Stock+Commandes,
  Dépenses+Statistiques, Compta/Factur-X.
