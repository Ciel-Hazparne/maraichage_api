# API de Supervision Agricole – Maraîchage BIO & Piment d'Espelette AOC BIO

Ce premier travail sur une API REST est la version simplifiée d'un de nos projets de BTS SN IR.

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

## Entités principales

### Mesure
```php
id: int
libelleMesure: LibelleMesure
valeur: float
createdAt: \DateTimeInterface
```

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
```php
id: int
libelle: string
unite: string
```
- Permet de référencer les types de mesure disponibles (ex : température, humidité)
- Inclut une unité (°C ou %)
- Accessible en lecture/écriture uniquement par un utilisateur authentifié avec `ROLE_ADMIN` depuis une IP autorisée

### MesureOutput (DTO)

```php
valeur: float
libelle: string
unite: string
createdAt: \DateTimeInterface
```
- Un normalizer personnalisé (MesureOutputNormalizer) permet d’adapter la sortie de l’API pour cette entité.
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
  - { path: ^/api/$, roles: PUBLIC_ACCESS }                     # Permet d'accéder à l'interface utilisateur de Swagger
  - { path: ^/api/docs, roles: PUBLIC_ACCESS }                  # Permet d'accéder à la documentation de l'interface utilisateur Swagger
  - { path: ^/api/auth_token, roles: PUBLIC_ACCESS }            # Autorise tout le monde à se connecter
  - { path: ^/api/mesures, roles: PUBLIC_ACCESS }               # Autorise GET pour l'affichage des graphiques et POST pour l'Arduino
  - { path: ^/api/libelle_mesures, roles: PUBLIC_ACCESS }       # Autorise les requêtes GET pour l'affichage des graphiques
  - { path: ^/api, roles: IS_AUTHENTICATED_FULLY }              # Toutes les autres routes /api/* nécessitent un JWT valide
```

---

## Exemple de routes

| Méthode | URI                  | Accès                   | Description                          |
|---------|----------------------|-------------------------|--------------------------------------|
| POST    | /api/auth_token      | Public                  | Obtenir un token JWT                 |
| GET     | /api/mesures         | Public                  | Liste des mesures                    |
| POST    | /api/mesures         | Public                  | Enregistrement automatique (Arduino) |
| PATCH   | /api/mesures/{id}    | Authentifié (IP + rôle) | Met à jour une mesure (test)         |
| DELETE  | /api/mesures/{id}    | Authentifié (IP + rôle) | Supprime une mesure                  |
| GET     | /api/libelle_mesures | APublic                 | Liste des types de mesures           |


---
## Structure partielle
```shell
├── src/
│   ├── Entity/
│   │   ├── Mesure.php
│   │   └── LibelleMesure.php
│   ├── Dto/
│   │   └── MesureOutput.php
│   └── Serializer/
│       └── MesureOutputNormalizer.php
├── config/
│   └── packages/
│       └── lexik_jwt_authentication.yaml
├── README.md
```
---
## Tests & développement

- L’authentification est stateless : aucun cookie/session. 
- Utiliser Postman, cURL ou un frontend JS avec Authorization: Bearer <token> dans les requêtes. 
- Pour les tests unitaires avec JWT : penser à simuler le token ou à désactiver temporairement le firewall dans l’environnement test.

---
## Technologies utilisées

- Symfony 6.4
- API Platform 4.1
- LexikJWTAuthenticationBundle
- Doctrine ORM
- MySQL
- PHP 8.2+
- Normalisation personnalisée via `MesureOutputNormalizer`
- Postman / cURL pour les tests

---
## Documentation
- [API Platform](https://api-platform.com/)
- [LexikJWTAuthenticationBundle](https://github.com/lexik/LexikJWTAuthenticationBundle)
- [Symfony Security](https://symfony.com/doc/6.4/security.html)