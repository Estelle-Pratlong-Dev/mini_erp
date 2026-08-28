# Mini ERP — Logiciel de gestion pour TPE

Application de gestion modulaire conçue pour répondre aux besoins quotidiens des petites entreprises.

> 🚧 Projet personnel — en cours de développement

## Le projet

Mini ERP est un projet personnel de logiciel de gestion destiné aux TPE.

L'objectif est de concevoir une application suffisamment modulaire pour pouvoir être adaptée à différents types d'activités et, à terme, proposée à plusieurs entreprises.

L'application couvre progressivement l'ensemble de la chaîne commerciale : gestion des contacts et du catalogue, suivi des projets, création des devis et contrats, facturation et gestion des stocks.

L'architecture est pensée dès le départ pour permettre de personnaliser les fonctionnalités disponibles selon les besoins de chaque entreprise.

## Fonctionnalités

Le projet comprend notamment :

- Gestion des contacts
- Gestion du catalogue
- Produits simples et produits composés
- Gestion des projets
- Création et suivi des devis
- Gestion des contrats
- Facturation
- Numérotation automatique des documents
- Gestion des stocks
- Listes de référence personnalisables
- Back-office d'administration

De nouvelles fonctionnalités sont ajoutées progressivement au cours du développement.

## Architecture modulaire

L'application est conçue pour pouvoir s'adapter aux besoins de différents clients.

Les fonctionnalités peuvent être organisées sous forme de **modules activables**, permettant à chaque entreprise de disposer uniquement des outils dont elle a besoin.

Cette approche permet d'envisager une même base applicative pour différents usages sans multiplier les versions spécifiques du logiciel.

## Rôles et permissions

La gestion des autorisations repose sur un système **RBAC (Role-Based Access Control)** stocké en base de données.

Cette architecture permet de gérer finement les droits des utilisateurs et d'éviter de figer les permissions directement dans le code.

## Administration et personnalisation

Un back-office basé sur **EasyAdmin** permet d'administrer les données et les paramètres de l'application.

Plusieurs listes de référence utilisées par le logiciel sont également personnalisables afin d'éviter d'imposer des valeurs métier identiques à toutes les entreprises.

## API

Une API REST est mise en place avec **API Platform**.

Elle permet d'exposer progressivement les données de l'application indépendamment de l'interface et prépare le projet à de futurs usages ou interfaces clientes.

## Qualité et gestion des données

Le projet intègre progressivement plusieurs mécanismes destinés à améliorer la fiabilité et la traçabilité des données :

- Tests avec PHPUnit
- Soft delete
- Audit des entités
- Gestion centralisée des rôles et permissions
- Contraintes et règles métier au niveau applicatif

## Stack technique

### Back-end

- PHP
- Symfony 8
- Doctrine ORM
- MySQL
- API Platform

### Administration et interface

- EasyAdmin
- Twig
- Bootstrap

### Tests et outils

- PHPUnit
- Git

## Développement assisté par IA

Ce projet est développé avec l'assistance d'outils d'intelligence artificielle.

L'IA est utilisée comme outil d'aide à la conception et à l'implémentation, notamment pour accélérer le développement et explorer différentes solutions techniques.

Je définis les besoins fonctionnels, les règles métier et les évolutions attendues du logiciel, puis je suis, teste et vérifie le fonctionnement des développements réalisés.

Le projet me permet également d'approfondir et d'actualiser progressivement ma pratique de **Symfony**, framework que j'avais initialement découvert et utilisé durant ma formation.

## Objectifs du projet

À terme, l'objectif est de disposer d'une application :

- modulaire ;
- personnalisable selon l'entreprise ;
- maintenable ;
- exploitable par différents types de TPE ;
- capable d'évoluer sans multiplier les développements spécifiques.

Le projet constitue également un terrain d'expérimentation et d'apprentissage autour de l'architecture d'une application métier moderne avec Symfony.

## Démonstration

🎥 **Démo vidéo à venir**

L'application nécessitant un environnement configuré et un compte utilisateur, elle n'est pas actuellement proposée sous forme de démonstration publique librement accessible.

Une démonstration vidéo permettra de présenter les principales fonctionnalités et les différents modules sans exposer l'environnement applicatif.

## Statut

🚧 **Développement en cours**

Le périmètre fonctionnel et l'architecture continuent d'évoluer au fur et à mesure du développement.
