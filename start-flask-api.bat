@echo off
echo Démarrage de l'API Flask pour la détection du cancer cervical...

cd flask_api

echo Vérification de Python...

:: Tester différentes commandes Python
python --version >nul 2>&1
if %errorlevel% == 0 (
    set PYTHON_CMD=python
    set PIP_CMD=pip
    goto :python_found
)

py --version >nul 2>&1
if %errorlevel% == 0 (
    set PYTHON_CMD=py
    set PIP_CMD=py -m pip
    goto :python_found
)

python3 --version >nul 2>&1
if %errorlevel% == 0 (
    set PYTHON_CMD=python3
    set PIP_CMD=pip3
    goto :python_found
)

echo ❌ ERREUR: Python n'est pas installé ou pas dans le PATH
echo.
echo Solutions:
echo 1. Installez Python depuis https://www.python.org/downloads/
echo 2. Assurez-vous de cocher "Add Python to PATH" lors de l'installation
echo 3. Redémarrez votre invite de commande après installation
echo 4. Consultez INSTALLATION_PYTHON_WINDOWS.md pour plus de détails
echo.
pause
exit /b 1

:python_found
echo ✅ Python trouvé: %PYTHON_CMD%

echo Installation des dépendances Python...
%PIP_CMD% install -r requirements.txt
if %errorlevel% neq 0 (
    echo ❌ Erreur lors de l'installation des dépendances
    echo Essayez manuellement: %PIP_CMD% install flask flask-cors tensorflow numpy pillow
    pause
    exit /b 1
)

echo Vérification du modèle...
if not exist "mon_modele.h5" (
    echo ❌ ERREUR: Le fichier mon_modele.h5 n'existe pas dans le dossier flask_api
    echo Veuillez copier votre modèle dans ce dossier
    echo.
    pause
    exit /b 1
)

echo ✅ Modèle trouvé: mon_modele.h5
echo.
echo 🚀 Démarrage de l'API Flask...
echo L'API sera accessible sur: http://localhost:5000
echo Appuyez sur Ctrl+C pour arrêter
echo.

%PYTHON_CMD% app.py

pause