# API de Supervision Agricole – Maraîchage BIO & Piment d'Espelette AOC BIO

Ce travail sur une API REST est la version simplifiée d'un de nos projets de BTS SN IR.

## Table des matières

- [Objectif du système](#objectif-du-système)
- [Ressources principales](#ressources-principales)
    - [Mesure](#mesure)
    - [LibelleMesure](#libellemesure)
- [Sécurité & filtrage IP](#sécurité--filtrage-ip)
- [Authentification JWT](#authentification-jwt)
    - [Exemple CURL pour générer un token](#exemple-curl-pour-générer-un-token)
    - [Exemple CURL pour récupérer les mesures](#exemple-curl-pour-récupérer-les-mesures)
- [Configuration de sécurité](#configuration-de-sécurité)
- [Tests](#tests)
- [Technologies utilisées](#technologies-utilisées)

---

## Objectif du système

L’API s’intègre dans un système global de supervision agricole destiné à :

- Surveiller température et humidité en plein champ (parcelle isolée)
- Surveiller les mêmes données sur un site distant
- Piloter un système de ventilation (via automate programmable)
- Enregistrer, archiver et restituer les mesures dans une base de données
- Permettre l’affichage et l’exploitation des mesures

Cette API Symfony 6.4 + API Platform permet donc de superviser une exploitation agricole répartie sur plusieurs sites, en intégrant des mesures physiques (température, humidité), des règles de sécurité IP, et une authentification JWT.

---

## Ressources principales

### Mesure

- Représente une mesure physique prise par un capteur.
- Structure de sortie personnalisée via un DTO `MesureOutput`
- Accessible en écriture (POST) sans authentification pour un microcontrôleur distant (ex. Arduino)
- Accès en lecture restreint pour les autres requêtes par IP et rôle (`ROLE_ADMIN`)

#### Exemple de structure de sortie (GET /api/mesures/13528)

```json
{
  "valeur": 22.5,
  "unite": "°C",
  "libelle": "temp_champ",
  "createdAt": "2025-07-20T10:15:00+00:00"
}
```

### LibelleMesure

- Permet de référencer les types de mesure disponibles (ex : température, humidité)
- Inclut une unité (°C ou %)
- Accessible en lecture/écriture uniquement par un utilisateur authentifié avec `ROLE_ADMIN` depuis une IP autorisée

---

## Sécurité & filtrage IP

La sécurité est appliquée au niveau des ressources via les attributs `security` :

### Lecture (GET, GET Collection)

- Réservée à `ROLE_ADMIN`
- Accessible uniquement depuis :
    - `127.0.0.1`
    - `10.0.0.102`
    - ou tout le réseau `10.0.0.0/16` (optionnel)

### Écriture (POST) sur `/api/mesures`

- Accès public autorisé pour les microcontrôleurs

---

## Authentification JWT

L'authentification s’effectue via le point d’entrée suivant :

- **Route :** `POST /api/auth_token`
- **Requête JSON :**

```json
{
  "email": "user@example.com",
  "password": "motdepasse"
}
```

- **Réponse (exemple) :**

```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJh..."
}
```

### Exemple CURL pour générer un token

```shell
curl -X POST http://localhost:8000/api/auth_token \
  -H "Content-Type: application/json" \
  -d '{"email":"admin.api@ciel-ir.eh", "password":"mot de passe de la section"}'
```

### Exemple CURL pour récupérer les mesures

```shell
curl -X GET http://localhost:8000/api/mesures \
  -H "Authorization: Bearer VOTRE_TOKEN"
```

---

## Configuration de sécurité

### Extrait du `security.yaml` :

```yaml
access_control:
    - { path: ^/api/$, roles: PUBLIC_ACCESS }
    - { path: ^/api/docs, roles: PUBLIC_ACCESS }
    - { path: ^/api/auth_token, roles: PUBLIC_ACCESS }
    - { path: ^/api/mesures$, roles: PUBLIC_ACCESS }
    - { path: ^/api, roles: IS_AUTHENTICATED_FULLY }
```

---

## Tests

Pour les environnements de test, les algorithmes de hachage sont allégés pour améliorer la vitesse :

```yaml
when@test:
    security:
        password_hashers:
            Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface:
                algorithm: auto
                cost: 4
                time_cost: 3
                memory_cost: 10
```

---

## Technologies utilisées

- Symfony 6.4
- API Platform 4.1
- LexikJWTAuthenticationBundle
- Doctrine ORM
- MySQL
- Normalisation personnalisée via `MesureOutputNormalizer`

