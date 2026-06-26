# Backlog — idées notées (non urgentes)

Idées à intégrer plus tard, sans priorité immédiate.

## Confort / UI
- **Fil d'Ariane (breadcrumbs)** sur les pages de l'appli de base.
- **Photo de présentation sur les projets** (réutiliser le mécanisme d'upload existant
  des pièces jointes / champ image dédié).

## Exports
- **Export des listes en Excel** (idéalement via une librairie type **PhpSpreadsheet**).
  Alternative simple : export CSV natif.
- **Export comptable des listes** (factures, dépenses…) : format adapté à la compta
  (journal des ventes, écritures, ou CSV/FEC selon besoin).

## Filtres & recherche
- **Filtrer les listes** (contacts, produits, projets, contrats, factures…) :
  par statut, date, client, montant, recherche texte. (côté API : déjà natif via API Platform.)

## Documents imprimables
- **Version téléchargeable / imprimable des factures et contrats**, **personnalisable par client**
  (en-tête, logo, mentions, mise en page).
  - Piste : génération **en interne** (gabarit Twig → HTML → PDF) pour garder le contrôle et
    ne pas envoyer de données financières à un tiers.
  - Historique : l'entreprise utilisait un **outil externe intégré** pour les versions imprimables.

## Statistiques / BI
- **Metabase** (déjà utilisé en interne) pour l'analyse avancée : se branche directement sur la base.
  - Garder dans l'appli quelques **KPI simples** (CA, dépenses, marge) ; déléguer l'analyse poussée à Metabase.
  - Penser : vues/réplica de lecture pour découpler le schéma des tableaux de bord ;
    impact licence/multi-client si on revend.

## Réglementaire (déjà identifié ailleurs)
- Numérotation de factures séquentielle et sûre (Phase 3).
- Caisse certifiée **NF525** (si encaissement B2C) et **facturation électronique Factur-X**.
- Avant mise en ligne / API externe : **JWT + firewall `/api` stateless**.
