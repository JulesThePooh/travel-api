# Travel API

Organisation de voyages entre plusieurs personnes — Symfony 7.4 / PHP 8.5 / MariaDB / Vite.

## Installation

```bash
make init
```

- Site : http://localhost:8010 (https : https://localhost:8453)
- phpMyAdmin : http://localhost:8082 (root / root)
- MariaDB : localhost:3309

## Comptes de test (mot de passe `12345`)

| Email               | Rôle       |
|---------------------|------------|
| dev@drosalys.fr     | ROLE_ADMIN |
| alice@travel.fr     | ROLE_USER  |
| bob@travel.fr       | ROLE_USER  |
| charlie@travel.fr   | ROLE_USER  |

Les 26 autres users générés ont aussi `12345` comme mot de passe.

## Modèle

- **User** — organise des `Trip` (owner) et participe via `TripMember`
- **Trip** — titre, destination, dates, budget/pers., owner
- **TripMember** — liaison Trip ↔ User avec un statut (invité / participe / décliné)
- **Activity** — activité d'un voyage (catégorie, horaires, prix/pers., participants)
- **Expense** — dépense partagée (qui a payé, montant, partagée entre qui, activité liée optionnelle)

## Commandes utiles

```bash
make up / make down
make php            # shell dans le conteneur php
make console <cmd>  # bin/console
make fixtures       # recharge les fixtures
make npm <cmd>      # npm dans le conteneur node
```

## Front

Vite (`vite.config.js`), une seule entrée `assets/app.js` chargée dans `templates/base.html.twig` → appliquée sur toutes les pages.
Le conteneur `node` lance `npm run watch` automatiquement.
