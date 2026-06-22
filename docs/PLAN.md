# Plan — mini_erp : ERP modulaire pour TPE (Symfony)

## Contexte

Création d'un **mini-ERP pour petites entreprises** dans le dossier vide `C:\laragon\www\mini_erp`,
avec un **objectif de commercialisation** auprès de plusieurs TPE.

Besoins (facturation, ventes directes, stocks, CA, dépenses, stats) organisés autour d'une chaîne
**Projet → Contrat → Facture**, avec deux interfaces distinctes (admin vs usage quotidien).

Contraintes décisives :
- **Configuration centralisée** : infos société, TVA, numérotation… au même endroit.
- **Modules activables/désactivables** : vendre le logiciel selon l'usage de chaque client.
- **Deux interfaces** : un back-office **EasyAdmin** réservé aux admins (utilisateurs + infos société +
  activation modules) et une **« appli de base »** distincte, plus simple, pour les utilisateurs courants.
- **Rôles granulaires à prévoir** : pouvoir, à terme, donner accès à certains modules à certains rôles
  (structure prévue dès maintenant, activée plus tard).

Stack : **Symfony 8.x + Doctrine + MySQL/MariaDB**. EasyAdmin 5 pour le back-office admin.
Appli de base en **Twig + Bootstrap 5 + Stimulus (Symfony UX)**, rendu serveur (simple à maintenir,
saisie caisse fluide via un peu de JS). PHP 8.4, Composer 2.9, Symfony CLI 5.16 présents.
Réutilisation depuis `mairie-erp` : `TimestampableTrait`, `CreatedByTrait`, enums typés, validation.

---

## Les deux interfaces

### A. Back-office admin — EasyAdmin `/admin` (ROLE_ADMIN uniquement)
Configuration et administration : **Utilisateurs**, **Société (paramètres)**, **Modules (activation)**.
Rien de la gestion quotidienne ici.

### B. Appli de base — interface métier (ROLE_USER, custom Twig)
L'ERP au quotidien : Tableau de bord, Contacts, Catalogue, Projets, Contrats, Factures,
**Vente directe (caisse)**, Stock, Dépenses, Statistiques. Visibilité des menus pilotée par les
**modules actifs** et (à terme) par les **droits du rôle**.

---

## Chaîne métier Projet → Contrat → Facture

- **Projet** : conteneur de regroupement. `nom` libre (client, marché, ou date du jour),
  `contact` opt., `date`, `statut`, `description`.
- **Contrat** : rattaché à un Projet. `type` (DEVIS/CONTRAT), lignes, montants HT/TVA/TTC,
  `statut` (**EN_ATTENTE**/ENVOYE/ACCEPTE/REFUSE/SIGNE), `dateValidite`. Convertible en facture.
- **Facture** : issue d'un Contrat (ou directe). `numero` auto (via `Societe`), dates,
  `statut` paiement (EN_ATTENTE/ENVOYEE/PAYEE/IMPAYEE/ANNULEE), lignes, totaux. Export PDF.
  **Avoir = facture à montant négatif** : pas d'entité ni de workflow distinct, seul le **libellé
  affiché** change (« Facture » si total ≥ 0, « Avoir » si total < 0).
- **Lignes** : `LigneDocument` générique partagée par Contrat et Facture (produit opt., désignation,
  quantité, prixUnitaireHT, tauxTva, montantHT).

## Pièces jointes
Entité **`PieceJointe`** : `fichier` (chemin), `nomOriginal`, `typeMime`, `taille`, `dateAjout`,
+ relations **optionnelles** vers `Projet`, `Contrat` et `Facture` (une pièce peut être rattachée à
l'un, ou aux trois). Permet de joindre mails, PDF, images, documents générés, etc. Upload géré via
VichUploaderBundle (ou gestion fichier maison) avec stockage dans `var/uploads/` ou `public/uploads/`.

## Commande (achats fournisseurs)
Entité **`Commande`** + **`LigneCommande`** : `fournisseur` (Contact type FOURNISSEUR), date,
`statut` (EN_ATTENTE/COMMANDEE/RECUE/ANNULEE), lignes (produit, quantité, prixAchatHT, tauxTva).
À la **réception** (statut RECUE) → génère des **mouvements de stock en entrée** **et** crée/renseigne
une **`Depense`** correspondante. Relie ainsi achats ↔ stock ↔ dépenses.

## Vente directe (module caisse)
Module **dédié et distinct** de la facturation. Saisie ultra-rapide : sélection des produits
(boutons/recherche), mode de paiement, validation → enregistre une `VenteDirecte` + lignes,
**décrémente le stock**, alimente le CA. **Pas de facture imposée** (B2C : ticket sur demande,
agrégation journalière). Ticket PDF optionnel + action « convertir en facture » au besoin.
⚠️ **Jalon réglementaire** : encaisser des paiements B2C pour un assujetti TVA impose un logiciel
**certifié NF525** (loi anti-fraude) — à traiter avant commercialisation de la caisse.

---

## Rôles & droits (RBAC piloté par la base de données)

Modèle **données** (pas de rôles codés en dur) :
- **`Permission`** (droit) : `code` (ex. `FACTURE_VOIR`, `FACTURE_CREER`, `STOCK_GERER`…), `libelle`,
  `description`, `module` (rattachement au `CodeModule` concerné).
- **`Role`** : `code` (ex. `ADMIN`, `COMMERCIAL`, `CAISSE`), `libelle`.
- **`Role ↔ Permission`** : Many-to-Many → **chaque droit est relié à un ou plusieurs rôles**, géré
  depuis l'admin (lister les droits, cocher les rôles).
- **`User ↔ Role`** : Many-to-Many → un utilisateur a un ou plusieurs rôles.

Intégration Symfony : `User::getRoles()` renvoie les codes de rôles **+** les codes de permissions
effectives (aplatis), afin que `#[IsGranted('FACTURE_CREER')]` et les Voters fonctionnent nativement.
Un rôle `ADMIN` (super-admin) court-circuite les vérifications.
- **Phase 0** : entités `Permission`/`Role` + liaisons + intégration dans le `User` + seed des droits
  par module (génération auto d'un jeu de permissions standard VOIR/CREER/MODIFIER/SUPPRIMER par module)
  + écrans de gestion dans EasyAdmin.
- **Application fine** (verrouiller chaque écran de l'appli de base sur sa permission) : posée en
  Phase 0 pour l'admin, généralisée à mesure que les modules métier sortent.

## Modules — entité `Module` + service `ModuleManager`
- `Module` : `code` (enum `CodeModule`), `nom`, `description`, `actif`.
- Codes : `CONTACTS`, `CATALOGUE`, `PROJETS`, `CONTRATS`, `FACTURATION`, `VENTE_DIRECTE`,
  `STOCK`, `DEPENSES`, `STATISTIQUES`, `COMPTA`.
- `ModuleManager::isEnabled(CodeModule)` (caché) → pilote les menus (admin + appli de base) et une
  garde d'accès (`EventSubscriber`) qui bloque l'URL des fonctions de modules désactivés.

## Configuration société — entité `Societe` (singleton, ADMIN)
`raisonSociale`, `formeJuridique`, `siret`, `numTva`, `adresse`, `codePostal`, `ville`, `pays`,
`telephone`, `email`, `siteWeb`, `logo`, `capital`, `rcs`, `iban`, `bic`, `tauxTvaDefaut`, `devise`,
`mentionsLegales`, `conditionsPaiement`, `prefixeFacture`, `prochainNumeroFacture`.

---

## Modèle de données cible

| Domaine | Entités |
|---------|---------|
| Socle (ADMIN) | `User`, `Societe`, `Module`, `Role`, `Permission` (M2M Role↔Permission, M2M User↔Role) |
| Contacts | `Contact` |
| Catalogue | `Produit` |
| Projets/Contrats | `Projet`, `Contrat`, `LigneDocument` |
| Facturation | `Facture` (réutilise `LigneDocument` ; avoir = montant négatif) |
| Pièces jointes | `PieceJointe` (liée à Projet/Contrat/Facture) |
| Vente directe | `VenteDirecte`, `LigneVente` |
| Achats | `Commande`, `LigneCommande` (→ stock + dépenses) |
| Stock | `MouvementStock` |
| Dépenses | `Depense` |
| Statistiques | (calculs, pas d'entité) |
| Compta | exports + facturation électronique Factur-X |

### Enums (`src/Enum/`)
`CodeModule`, `TypeContact`, `TypeProduit`, `StatutProjet`, `TypeContrat`, `StatutContrat`
(EN_ATTENTE/ENVOYE/ACCEPTE/REFUSE/SIGNE), `StatutFacture`, `StatutCommande`, `ModePaiement`,
`TypeMouvementStock`. (Pas de `TypeFacture` : l'avoir est déduit du signe du montant.)

---

## Phases d'implémentation (itératif, testable à chaque étape)

**Phase 0 — Socle + back-office admin (maintenant)**
Bootstrap Symfony + MySQL ; traits ; `User`/auth ; EasyAdmin `/admin` (rôle ADMIN) avec
**Utilisateurs**, **Société**, **Modules**, **Rôles** & **Permissions** ; `ModuleManager` + garde ;
RBAC en BDD (`Role`/`Permission` + liaisons, intégration `getRoles()`, Voters) + seed des droits ;
migration initiale ; commande de création du premier admin.

**Phase 1 — Squelette appli de base + données (maintenant)**
Layout Twig + Bootstrap, connexion utilisateur, menu role/module-aware, tableau de bord vide ;
`Contact` et `Produit` avec écrans liste/création/édition.

**Phase 2 — Projets + Contrats + Pièces jointes** : `Projet`, `Contrat` + `LigneDocument`, devis,
conversion en facture ; `PieceJointe` (upload/joindre des documents au projet/contrat).

**Phase 3 — Facturation** : `Facture`, numérotation auto, totaux, statuts, **avoir = montant négatif**,
pièces jointes facture, **PDF (dompdf)**.

**Phase 4 — Vente directe (caisse)** : `VenteDirecte` + UI rapide, impact stock, ticket, total du jour.

**Phase 5 — Stock + Commandes (achats)** : `MouvementStock`, recalcul `stockActuel`, alertes `stockMin` ;
`Commande`/`LigneCommande` → réception qui alimente le stock **et** génère une dépense.

**Phase 6 — Dépenses + Statistiques** : `Depense`, dashboard CA/dépenses/marge, graphiques (Chart.js).

**Phase 7 — Compta / facturation électronique** : exports comptables CSV + **Factur-X** ; cadrage NF525.

---

## Fichiers principaux (Phases 0–1)

- `mini_erp/.env.local` — `DATABASE_URL` MySQL
- `mini_erp/src/Trait/{TimestampableTrait,CreatedByTrait}.php`
- `mini_erp/src/Entity/{User,Societe,Module,Role,Permission,Contact,Produit}.php`
- `mini_erp/src/Enum/{CodeModule,TypeContact,TypeProduit}.php`
- `mini_erp/src/Service/ModuleManager.php`
- `mini_erp/src/EventSubscriber/ModuleAccessSubscriber.php`
- `mini_erp/src/Security/` (voters) + `config/packages/security.yaml`
- `mini_erp/src/Controller/Admin/{DashboardController,UserCrudController,SocieteCrudController,ModuleCrudController,RoleCrudController,PermissionCrudController}.php`
- `mini_erp/src/Controller/{SecurityController,DashboardController,ContactController,ProduitController}.php`
- `mini_erp/templates/` (base, layout appli de base, contacts, produits)
- `mini_erp/migrations/Version*.php`

## Vérification (end-to-end)

1. `cd mini_erp && symfony server:start` → démarre ; `doctrine:schema:validate` OK.
2. **Admin** : `/admin` accessible en ROLE_ADMIN → Utilisateurs + Société + Modules ; saisie société persistée.
3. **Modules** : désactiver « Catalogue » → disparaît du menu appli de base **et** URL bloquée ; réactiver → OK.
4. **Appli de base** : un ROLE_USER se connecte, n'a **pas** accès à `/admin`, voit le tableau de bord + modules actifs.
5. **Contacts / Produits** : création/édition, validation, audit (`createdBy`/`createdAt`).
6. Phases suivantes testées à leur livraison.

## Évolutions futures / points ouverts
- **Multi-tenant** : v1 = 1 installation = 1 société. Isolation multi-sociétés en base unique = évolution lourde.
- **RBAC fin par module** : schéma posé en Phase 0, application des droits à activer ultérieurement.
- **Caisse certifiée NF525** et **facturation électronique** (Factur-X / e-reporting) : jalons réglementaires FR.
