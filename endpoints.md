# API Endpoints MatchRoom

## UserController

- `GET /user` - Liste tous les utilisateurs
- `GET /user/{id}` - Affiche un utilisateur spécifique
- `GET /user/new` - Formulaire pour créer un nouvel utilisateur
- `POST /user/new` - Crée un nouvel utilisateur
- `GET /user/{id}/edit` - Formulaire pour modifier un utilisateur
- `POST /user/{id}/edit` - Modifie un utilisateur
- `POST /user/{id}` - Supprime un utilisateur

## RoomController

- `GET /room` - Liste toutes les chambres
- `GET /room/{id}` - Affiche une chambre spécifique
- `GET /room/new` - Formulaire pour créer une nouvelle chambre
- `POST /room/new` - Crée une nouvelle chambre
- `GET /room/{id}/edit` - Formulaire pour modifier une chambre
- `POST /room/{id}/edit` - Modifie une chambre
- `POST /room/{id}` - Supprime une chambre

## BookingController

- `GET /booking` - Liste toutes les réservations
- `GET /booking/{id}` - Affiche une réservation spécifique
- `GET /booking/new` - Formulaire pour créer une nouvelle réservation
- `POST /booking/new` - Crée une nouvelle réservation
- `GET /booking/{id}/edit` - Formulaire pour modifier une réservation
- `POST /booking/{id}/edit` - Modifie une réservation
- `POST /booking/{id}` - Supprime une réservation

## MatchingController

- `GET /matching` - Liste tous les matchings
- `GET /matching/{id}` - Affiche un matching spécifique
- `GET /matching/new` - Formulaire pour créer un nouveau matching
- `POST /matching/new` - Crée un nouveau matching
- `GET /matching/{id}/edit` - Formulaire pour modifier un matching
- `POST /matching/{id}/edit` - Modifie un matching
- `POST /matching/{id}` - Supprime un matching

## SearchController

- `GET /search` - Liste toutes les recherches
- `GET /search/{id}` - Affiche une recherche spécifique
- `GET /search/new` - Formulaire pour créer une nouvelle recherche
- `POST /search/new` - Crée une nouvelle recherche
- `GET /search/{id}/edit` - Formulaire pour modifier une recherche
- `POST /search/{id}/edit` - Modifie une recherche
- `POST /search/{id}` - Supprime une recherche

## SecurityController

- `GET /api/logout` - Déconnexion de l'utilisateur
- `GET /api/token/refresh` - Rafraîchissement du token JWT

## UserBadgeController (supposé similaire aux autres contrôleurs)

- `GET /user-badge` - Liste tous les badges utilisateur
- `GET /user-badge/{id}` - Affiche un badge utilisateur spécifique
- `GET /user-badge/new` - Formulaire pour créer un nouveau badge utilisateur
- `POST /user-badge/new` - Crée un nouveau badge utilisateur
- `GET /user-badge/{id}/edit` - Formulaire pour modifier un badge utilisateur
- `POST /user-badge/{id}/edit` - Modifie un badge utilisateur
- `POST /user-badge/{id}` - Supprime un badge utilisateur

## UserPreferenceController (supposé similaire aux autres contrôleurs)

- `GET /user-preference` - Liste toutes les préférences utilisateur
- `GET /user-preference/{id}` - Affiche une préférence utilisateur spécifique
- `GET /user-preference/new` - Formulaire pour créer une nouvelle préférence utilisateur
- `POST /user-preference/new` - Crée une nouvelle préférence utilisateur
- `GET /user-preference/{id}/edit` - Formulaire pour modifier une préférence utilisateur
- `POST /user-preference/{id}/edit` - Modifie une préférence utilisateur
- `POST /user-preference/{id}` - Supprime une préférence utilisateur

## TransactionController (supposé similaire aux autres contrôleurs)

- `GET /transaction` - Liste toutes les transactions
- `GET /transaction/{id}` - Affiche une transaction spécifique
- `GET /transaction/new` - Formulaire pour créer une nouvelle transaction
- `POST /transaction/new` - Crée une nouvelle transaction
- `GET /transaction/{id}/edit` - Formulaire pour modifier une transaction
- `POST /transaction/{id}/edit` - Modifie une transaction
- `POST /transaction/{id}` - Supprime une transaction

## ServiceController (supposé similaire aux autres contrôleurs)

- `GET /service` - Liste tous les services
- `GET /service/{id}` - Affiche un service spécifique
- `GET /service/new` - Formulaire pour créer un nouveau service
- `POST /service/new` - Crée un nouveau service
- `GET /service/{id}/edit` - Formulaire pour modifier un service
- `POST /service/{id}/edit` - Modifie un service
- `POST /service/{id}` - Supprime un service

## RefreshTokenController (supposé similaire aux autres contrôleurs)

- `GET /refresh-token` - Liste tous les refresh tokens
- `GET /refresh-token/{id}` - Affiche un refresh token spécifique
- `GET /refresh-token/new` - Formulaire pour créer un nouveau refresh token
- `POST /refresh-token/new` - Crée un nouveau refresh token
- `GET /refresh-token/{id}/edit` - Formulaire pour modifier un refresh token
- `POST /refresh-token/{id}/edit` - Modifie un refresh token
- `POST /refresh-token/{id}` - Supprime un refresh token

## RatingController (supposé similaire aux autres contrôleurs)

- `GET /rating` - Liste toutes les évaluations
- `GET /rating/{id}` - Affiche une évaluation spécifique
- `GET /rating/new` - Formulaire pour créer une nouvelle évaluation
- `POST /rating/new` - Crée une nouvelle évaluation
- `GET /rating/{id}/edit` - Formulaire pour modifier une évaluation
- `POST /rating/{id}/edit` - Modifie une évaluation
- `POST /rating/{id}` - Supprime une évaluation

## OfferController (supposé similaire aux autres contrôleurs)

- `GET /offer` - Liste toutes les offres
- `GET /offer/{id}` - Affiche une offre spécifique
- `GET /offer/new` - Formulaire pour créer une nouvelle offre
- `POST /offer/new` - Crée une nouvelle offre
- `GET /offer/{id}/edit` - Formulaire pour modifier une offre
- `POST /offer/{id}/edit` - Modifie une offre
- `POST /offer/{id}` - Supprime une offre

## HotelController (supposé similaire aux autres contrôleurs)

- `GET /hotel` - Liste tous les hôtels
- `GET /hotel/{id}` - Affiche un hôtel spécifique
- `GET /hotel/new` - Formulaire pour créer un nouvel hôtel
- `POST /hotel/new` - Crée un nouvel hôtel
- `GET /hotel/{id}/edit` - Formulaire pour modifier un hôtel
- `POST /hotel/{id}/edit` - Modifie un hôtel
- `POST /hotel/{id}` - Supprime un hôtel

## FavoriteController (supposé similaire aux autres contrôleurs)

- `GET /favorite` - Liste tous les favoris
- `GET /favorite/{id}` - Affiche un favori spécifique
- `GET /favorite/new` - Formulaire pour créer un nouveau favori
- `POST /favorite/new` - Crée un nouveau favori
- `GET /favorite/{id}/edit` - Formulaire pour modifier un favori
- `POST /favorite/{id}/edit` - Modifie un favori
- `POST /favorite/{id}` - Supprime un favori

## BadgeController (supposé similaire aux autres contrôleurs)

- `GET /badge` - Liste tous les badges
- `GET /badge/{id}` - Affiche un badge spécifique
- `GET /badge/new` - Formulaire pour créer un nouveau badge
- `POST /badge/new` - Crée un nouveau badge
- `GET /badge/{id}/edit` - Formulaire pour modifier un badge
- `POST /badge/{id}/edit` - Modifie un badge
- `POST /badge/{id}` - Supprime un badge

## AmbianceController (supposé similaire aux autres contrôleurs)

- `GET /ambiance` - Liste toutes les ambiances
- `GET /ambiance/{id}` - Affiche une ambiance spécifique
- `GET /ambiance/new` - Formulaire pour créer une nouvelle ambiance
- `POST /ambiance/new` - Crée une nouvelle ambiance
- `GET /ambiance/{id}/edit` - Formulaire pour modifier une ambiance
- `POST /ambiance/{id}/edit` - Modifie une ambiance
- `POST /ambiance/{id}` - Supprime une ambiance
