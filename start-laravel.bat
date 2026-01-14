@echo off
echo Démarrage de l'application Laravel...

echo Nettoyage du cache...
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo Mise à jour de l'autoloader...
composer dump-autoload

echo Génération de la clé d'application (si nécessaire)...
php artisan key:generate --show

echo Création du lien symbolique pour le stockage...
php artisan storage:link

echo Vérification des permissions...
if not exist "storage\app\public\admin-analyses" mkdir "storage\app\public\admin-analyses"

echo.
echo ✅ Configuration terminée!
echo 🚀 Démarrage du serveur Laravel...
echo.
echo Accès:
echo - Application: http://localhost:8000
echo - Dashboard Admin: http://localhost:8000/admin/dashboard
echo.

php artisan serve

pause