# Cervical Clinic - Système de Détection du Cancer Cervical

Application web de gestion de clinique avec détection automatique du cancer cervical par intelligence artificielle.

## 🎯 Fonctionnalités

- **Gestion des patients** : Création, modification et suivi des dossiers patients
- **Analyse par IA** : Détection automatique du cancer cervical via modèle TensorFlow
- **Dashboard administrateur** : Suivi des analyses, statistiques et gestion des utilisateurs
- **Sécurité** : Chiffrement des images médicales, authentification sécurisée
- **Notifications email** : Envoi automatique des résultats d'analyse
- **Interface multilingue** : Support français et anglais

## 🛠️ Technologies

- **Backend** : Laravel 11 (PHP 8.2+)
- **Frontend** : Blade, Vite, TailwindCSS
- **Base de données** : MySQL
- **IA** : Flask API + TensorFlow pour la détection
- **Sécurité** : Chiffrement AES-256 pour les images médicales

## 📋 Prérequis

- PHP 8.2 ou supérieur
- Composer
- MySQL 5.7+
- Python 3.8+ (pour l'API Flask)
- Node.js et NPM

## 🚀 Installation

### 1. Cloner le projet

```bash
git clone https://github.com/votre-username/cervical-clinic.git
cd cervical-clinic
```

### 2. Installer les dépendances PHP

```bash
composer install
```

### 3. Configurer l'environnement

```bash
copy .env.example .env
php artisan key:generate
```

Modifiez le fichier `.env` avec vos paramètres :

```env
DB_DATABASE=cervicare
DB_USERNAME=root
DB_PASSWORD=votre_mot_de_passe

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre_email@gmail.com
MAIL_PASSWORD=votre_mot_de_passe_app
```

### 4. Créer la base de données

```bash
php artisan migrate
php artisan db:seed
```

### 5. Installer les dépendances Python

**Windows :**
```bash
install-python-dependencies.bat
```

**Linux/Mac :**
```bash
cd flask_api
pip install -r requirements.txt
```

### 6. Configurer le modèle IA

⚠️ **Important** : Le modèle TensorFlow n'est pas inclus dans le dépôt (trop volumineux).


**Option 1 - Télécharger le modèle pré-entraîné :**
- Téléchargez depuis : https://drive.google.com/drive/folders/1gcvpT0XxrJWvnkWqVRhVybWR1wUdvdK-?usp=drive_link
- Placez-le dans `flask_api/mon_modele.h5`

**Option 2 - Mode test sans modèle :**
- L'API peut fonctionner en mode test sans le modèle pour les tests d'intégration

## 🎮 Démarrage

### Windows

**Terminal 1 - Laravel :**
```bash
start-laravel.bat
```

**Terminal 2 - API Flask :**
```bash
start-flask-api.bat
```

### Linux/Mac

**Terminal 1 - Laravel :**
```bash
php artisan serve
```

**Terminal 2 - API Flask :**
```bash
cd flask_api
python app.py
```

## 🌐 Accès à l'application

- **Application** : http://localhost:8000
- **Dashboard Admin** : http://localhost:8000/admin/dashboard
- **API Flask** : http://localhost:5000

### Comptes par défaut

**Administrateur :**
- Email : hindzabrati03@gmail.com
- Mot de passe : HIND@2003

**Médecin :**
- Email : imanearrach@gmail.com
- Mot de passe : IMANE@2003

**Patient :**
-Email: salmabender@gmail.com
-Mot de passe: SALMA@2004

## 📁 Structure du projet

```
cervical-clinic/
├── app/                    # Code Laravel (Controllers, Models, Services)
├── flask_api/              # API Flask pour la détection IA
├── resources/              # Vues Blade, CSS, JS
├── database/               # Migrations et seeders
├── public/                 # Assets publics
├── storage/                # Fichiers uploadés (images chiffrées)
└── routes/                 # Routes web et API
```

## 🔒 Sécurité

- Les images médicales sont chiffrées avec AES-256
- Authentification Laravel Breeze
- Protection CSRF sur tous les formulaires
- Validation des données côté serveur
- Logs de sécurité pour les actions sensibles

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à ouvrir une issue ou une pull request.

## 📝 License

Ce projet est sous licence MIT.
