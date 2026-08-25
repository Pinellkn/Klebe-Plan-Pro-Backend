# Klébé Plan Pro — Backend (tâche de Pinel)

Sprint V1.3 (23 → 31 août 2026). Basé sur `Klebe_Plan_Pro_Repartition_Taches.pdf`
et `DOC-20260818-WA0015.pdf` (analyse stratégique).

**Ma tâche (Pinel — Données & API RDV) :**
> Créer la structure de la base de données (rendez-vous, utilisateurs, quota) et
> coder les endpoints pour créer/modifier/supprimer un rendez-vous, ainsi que la
> gestion des permissions d'équipe.

Base de données : MySQL en production, SQLite possible en dev local
(`DB_CONNECTION=sqlite` dans `.env`, voir `.env.example`).

## ✅ Mes tâches terminées (Pinel)

**Structure de données**
- [x] Migration `entreprises` (nom, téléphone DG, plan, **quota** : quota_mensuel,
      quota_utilise, quota_packs_supplementaires, reset mensuel)
- [x] Migration `users` (rattachés à une entreprise, **rôle** proprietaire/assistante
      = base des permissions d'équipe) + password_reset_tokens + personal_access_tokens
      (Sanctum, prêt pour que Bilal branche l'auth WhatsApp dessus)
- [x] Migration `rendez_vous` (nom, date, heure, lieu, statut enrichi
      planifie/confirme/reporte/annule/manque/termine, suivi des 3 rappels,
      soft delete pour garder l'historique)
- [x] Modèles Eloquent : `Entreprise` (avec `quotaRestant()` / `quotaAtteint()`),
      `User` (avec `estProprietaire()`), `RendezVous`

**API & sécurité**
- [x] `RendezVousPolicy` : une assistante ne peut voir/modifier QUE les RDV de sa
      propre entreprise (sécurité multi-tenant)
- [x] `RendezVousController` : CRUD complet (index avec filtres statut/date +
      pagination, show, store, update, destroy) — scopé par entreprise
- [x] `TeamController` : liste équipe, ajouter une assistante, désactiver/réactiver,
      retirer — réservé au rôle "proprietaire" (permissions d'équipe)
- [x] `QuotaController` : lecture du quota restant (pour l'écran de Keira)
- [x] Form Requests de validation : `StoreRendezVousRequest`,
      `UpdateRendezVousRequest`, `AddTeamMemberRequest`
- [x] `RendezVousResource` : format JSON stable pour le front
- [x] Routes API (`routes/api.php`) prêtes à coller dans le projet principal
- [x] `AuthServiceProvider` : enregistrement de la policy (fichier prêt à fusionner)

**Qualité / mise en route**
- [x] `database/factories` (Entreprise, User, RendezVous) pour générer des données de test
- [x] `DemoSeeder` : jeu de données de démo pour brancher le front sans attendre
      l'inscription (compte `proprietaire@demo.klebeplan.test` / `password`)
- [x] `tests/Feature/RendezVousApiTest.php` : 6 tests (CRUD, isolation multi-tenant,
      filtres, soft delete, permissions équipe, calcul du quota)
- [x] `.env.example` avec la config MySQL (+ option SQLite pour dev local)

## ⏳ Ce qu'il me reste à intégrer (Pinel — nécessite le vrai projet + terminal)

- [ ] Coller ces fichiers dans le vrai projet Laravel (voir "Intégration" ci-dessous)
- [ ] Créer/compléter le `.env` avec les identifiants MySQL et lancer
      `php artisan migrate --seed --seeder=DemoSeeder`
- [ ] Fusionner `AuthServiceProvider.php` avec celui du projet principal s'il existe déjà
- [ ] Lancer `php artisan test --filter=RendezVousApiTest` pour valider
- [ ] Check-in du 27/08 : vérifier avec Shalom que le formulaire RDV du front
      colle bien aux champs attendus par `StoreRendezVousRequest`

## 🔧 Ce qui reste à faire par Bilal (WhatsApp & rappels)

D'après la répartition des tâches, Bilal doit : *"Coder l'authentification, brancher
l'API WhatsApp et programmer les 3 rappels automatiques (veille 18h, jour J 8h,
15 min avant), plus le système de quota (comptage et blocage)."*

Ce que j'ai déjà préparé côté données pour que ce soit simple à brancher :

1. **Authentification** — la table `users` a déjà `role` (proprietaire/assistante)
   et la table `personal_access_tokens` de Sanctum. Bilal doit :
   - `composer require laravel/sanctum` si pas déjà fait, puis publier sa config
   - Créer l'endpoint d'inscription : le PREMIER compte "proprietaire" doit créer
     son `entreprise` en même temps que son `user` (je ne l'ai pas fait pour éviter
     un conflit de code avec sa tâche auth)
   - Créer les endpoints login/logout avec Sanctum

2. **API WhatsApp & les 3 rappels automatiques** — la table `rendez_vous` a déjà
   les colonnes de suivi des rappels (veille/jour J/15 min) et le statut enrichi
   (planifie/confirme/reporte/annule/manque/termine). Bilal doit :
   - Brancher l'API WhatsApp (récupérer les credentials, tester l'envoi)
   - Créer un scheduler Laravel (`app/Console/Kernel.php`, tâche planifiée qui
     tourne chaque minute) qui lit les `rendez_vous` à venir et envoie le bon
     rappel au bon moment, puis marque le rappel comme envoyé
   - Gérer les échecs d'envoi (retry, log) pour ne pas bloquer les autres rappels

3. **Système de quota (comptage et blocage)** — la colonne `quota_utilise` et la
   méthode `Entreprise::quotaAtteint()` existent déjà (utilisées par mon
   `QuotaController` pour l'affichage côté Keira). Bilal doit uniquement :
   - Incrémenter `quota_utilise` à chaque envoi WhatsApp réussi
   - Vérifier `quotaAtteint()` AVANT d'envoyer un rappel, et bloquer l'envoi si
     le quota est dépassé (avec notification claire à l'assistante, comme
     recommandé dans l'analyse stratégique)

Une fois ces 3 points faits, la V1.3 sera complète côté rappels + auth.

## Intégration dans le projet Laravel principal

1. Copier `database/migrations/*`, `database/factories/*`, `database/seeders/DemoSeeder.php`
   → mêmes dossiers du projet
2. Copier `app/Models/*`, `app/Http/Controllers/Api/*`, `app/Http/Requests/*`,
   `app/Http/Resources/*`, `app/Policies/*`, `tests/Feature/RendezVousApiTest.php`
   aux mêmes emplacements
3. Ajouter le contenu de `routes/api.php` (ici) dans le `routes/api.php` existant
4. Dans `app/Providers/AuthServiceProvider.php` du projet principal, fusionner le
   tableau `$policies` (voir fichier fourni ici — ne pas écraser si un provider existe déjà) :
   ```php
   protected $policies = [
       \App\Models\RendezVous::class => \App\Policies\RendezVousPolicy::class,
   ];
   ```
5. `composer require laravel/sanctum` si pas déjà fait (Bilal s'en occupe pour l'auth)
6. Fusionner `.env.example` dans le `.env` réel
7. `php artisan migrate --seed --seeder=DemoSeeder`

## Endpoints créés

| Méthode | Route | Description |
|---|---|---|
| GET | `/api/rendez-vous` | Liste (filtres `?statut=`, `?date=`) |
| GET | `/api/rendez-vous/{id}` | Détail |
| POST | `/api/rendez-vous` | Créer |
| PUT/PATCH | `/api/rendez-vous/{id}` | Modifier |
| DELETE | `/api/rendez-vous/{id}` | Supprimer (soft delete) |
| GET | `/api/equipe` | Liste des membres de l'entreprise |
| POST | `/api/equipe` | Ajouter une assistante (proprietaire uniquement) |
| PATCH | `/api/equipe/{id}/desactiver` | Désactiver un membre |
| PATCH | `/api/equipe/{id}/reactiver` | Réactiver un membre |
| DELETE | `/api/equipe/{id}` | Retirer un membre |
| GET | `/api/quota` | Quota restant de l'entreprise |

## Arborescence de ce dossier

```
Klebe-Plan-Pro-Backend/
├── .env.example
├── README.md
├── app/
│   ├── Http/{Controllers/Api, Requests, Resources}/
│   ├── Models/
│   ├── Policies/
│   └── Providers/AuthServiceProvider.php
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/DemoSeeder.php
├── routes/api.php
└── tests/Feature/RendezVousApiTest.php
```

---
*Dernière mise à jour : 25 août 2026 — tâche Pinel complète côté code. README
réorganisé pour lister mes tâches faites et détailler ce qui reste à Bilal
(authentification, rappels WhatsApp, quota).*
