# School Management System - Système de Gestion Scolaire

Application complète de gestion scolaire développée avec Laravel 10, Jetstream (Inertia + React), et Tailwind CSS.

## 🎯 Fonctionnalités

- **Gestion des étudiants** : Inscription, profils, historique académique
- **Gestion des enseignants** : Profils, spécialisations, classes assignées
- **Gestion des classes** : Organisation par niveau et section
- **Gestion des matières** : Codes de cours, crédits, descriptions
- **Inscriptions** : Inscription des étudiants aux matières
- **Notes** : Suivi des évaluations (examens, devoirs, projets)
- **Présences** : Suivi quotidien de la présence des étudiants
- **Tableau de bord** : Statistiques et vue d'ensemble

## 🛠 Stack Technique

- **Backend** : Laravel 10 (PHP 8.1+)
- **Frontend** : React 18 avec Inertia.js
- **Styling** : Tailwind CSS
- **Authentification** : Laravel Jetstream
- **Base de données** : MySQL 8.0
- **Cache** : Redis
- **Conteneurisation** : Docker & Docker Compose

## 📋 Prérequis

- Docker & Docker Compose installés sur votre machine
- Git

## 🚀 Installation

### 1. Cloner le dépôt

```bash
git clone https://github.com/philipe-ngoie/school_manager.git
cd school_manager
```

### 2. Copier le fichier d'environnement

```bash
cp .env.example .env
```

### 3. Démarrer les conteneurs Docker

```bash
docker-compose up -d
```

### 4. Installer les dépendances

```bash
# Dépendances PHP
docker-compose exec app composer install

# Dépendances Node.js
docker-compose exec app npm install
```

### 5. Générer la clé d'application

```bash
docker-compose exec app php artisan key:generate
```

### 6. Exécuter les migrations et les seeders

```bash
docker-compose exec app php artisan migrate --seed
```

Cela va créer:
- 1 école de démonstration
- 20 enseignants
- 10 classes
- 15 matières
- 100 étudiants (10 par classe)
- Inscriptions aux matières
- Notes et présences

### 7. Compiler les assets

```bash
# En mode développement avec hot reload
docker-compose exec app npm run dev

# Ou en mode production
docker-compose exec app npm run build
```

### 8. Accéder à l'application

Ouvrez votre navigateur et accédez à : **http://localhost:8080**

## 🔐 Connexion

Pour vous connecter, vous devez d'abord créer un compte utilisateur :

```bash
docker-compose exec app php artisan tinker
```

Puis dans le terminal Tinker :

```php
User::create([
    'name' => 'Admin',
    'email' => 'admin@school.com',
    'password' => bcrypt('password'),
]);
exit
```

Connectez-vous avec :
- **Email** : admin@school.com
- **Mot de passe** : password

## 📦 Structure du Projet

```
school_manager/
├── app/
│   ├── Http/
│   │   ├── Controllers/         # Contrôleurs web
│   │   │   └── Api/            # Contrôleurs API
│   │   └── Resources/          # Ressources API
│   └── Models/                 # Modèles Eloquent
├── database/
│   ├── factories/              # Factories pour les tests
│   ├── migrations/             # Migrations de base de données
│   └── seeders/                # Seeders
├── docker/                     # Configuration Docker
│   ├── nginx/                  # Configuration Nginx
│   ├── php/                    # Configuration PHP
│   └── mysql/                  # Configuration MySQL
├── resources/
│   ├── js/
│   │   ├── Components/         # Composants React réutilisables
│   │   ├── Layouts/            # Layouts React
│   │   └── Pages/              # Pages Inertia React
│   └── css/                    # Styles CSS/Tailwind
├── routes/
│   ├── web.php                 # Routes web
│   └── api.php                 # Routes API
├── docker-compose.yml          # Configuration Docker Compose
├── Dockerfile                  # Dockerfile de l'application
└── Makefile                    # Commandes make utiles
```

## 🐳 Commandes Docker utiles

### Utilisation du Makefile

```bash
make help           # Afficher l'aide
make start          # Démarrer les conteneurs
make stop           # Arrêter les conteneurs
make restart        # Redémarrer les conteneurs
make install        # Installer les dépendances
make migrate        # Exécuter les migrations
make seed           # Exécuter les seeders
make fresh          # Migration fresh avec seeders
make test           # Exécuter les tests
make bash           # Accéder au bash du conteneur
make logs           # Afficher les logs
```

### Commandes Artisan

```bash
# Utiliser make
make artisan cmd="route:list"

# Ou directement avec docker-compose
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
docker-compose exec app php artisan route:list
docker-compose exec app php artisan tinker
```

### Commandes NPM

```bash
# Utiliser make
make npm cmd="run dev"

# Ou directement avec docker-compose
docker-compose exec app npm run dev
docker-compose exec app npm run build
```

## 🗄 Schéma de Base de Données

### Tables principales

- **schools** : Informations sur l'école
- **teachers** : Enseignants (nom, email, spécialisation, salaire)
- **school_classes** : Classes (niveau, section, enseignant, capacité)
- **subjects** : Matières (code, nom, crédits)
- **students** : Étudiants (nom, email, classe, informations parents)
- **enrollments** : Inscriptions étudiants-matières
- **grades** : Notes et évaluations
- **attendances** : Présences quotidiennes

### Relations

- Un enseignant peut avoir plusieurs classes
- Une classe appartient à un enseignant
- Une classe a plusieurs étudiants
- Un étudiant appartient à une classe
- Un étudiant peut s'inscrire à plusieurs matières
- Une matière peut avoir plusieurs étudiants inscrits
- Un étudiant a plusieurs notes
- Un étudiant a plusieurs enregistrements de présence

## 🧪 Tests

Exécuter les tests PHPUnit :

```bash
docker-compose exec app php artisan test
```

## 🔌 API REST

L'application expose une API REST complète pour toutes les ressources :

### Endpoints disponibles

```
GET    /api/students          # Liste des étudiants
POST   /api/students          # Créer un étudiant
GET    /api/students/{id}     # Détails d'un étudiant
PUT    /api/students/{id}     # Mettre à jour un étudiant
DELETE /api/students/{id}     # Supprimer un étudiant
```

Les mêmes endpoints sont disponibles pour :
- `/api/teachers`
- `/api/classes`
- `/api/subjects`
- `/api/enrollments`
- `/api/grades`
- `/api/attendances`

### Authentification API

L'API utilise Laravel Sanctum pour l'authentification. Incluez le token dans l'en-tête :

```
Authorization: Bearer {token}
```

## 📝 Notes importantes

### Jetstream

Ce projet utilise Laravel Jetstream avec la stack Inertia + React. Jetstream fournit :
- Authentification (login, registration, password reset)
- Gestion des profils utilisateurs
- Authentification à deux facteurs (2FA)
- Gestion des sessions
- API tokens

### Tailwind CSS

L'interface utilise Tailwind CSS avec le mode dark activé. Pour personnaliser les styles, modifiez `tailwind.config.js`.

### Vite

Le projet utilise Vite pour le bundling des assets. Configuration dans `vite.config.js`.

## 🐛 Dépannage

### Les conteneurs ne démarrent pas

```bash
docker-compose down
docker-compose up -d --build
```

### Erreur de permission

```bash
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Base de données vide

```bash
docker-compose exec app php artisan migrate:fresh --seed
```

### Assets non compilés

```bash
docker-compose exec app npm run build
```

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à ouvrir une issue ou une pull request.

## 📄 Licence

Ce projet est open source et disponible sous la [licence MIT](https://opensource.org/licenses/MIT).

## 👨‍💻 Auteur

Développé par **philipe-ngoie**

## 📞 Support

Pour toute question ou problème, ouvrez une issue sur GitHub.
