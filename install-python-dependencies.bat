@echo off
echo Installation manuelle des dépendances Python...
echo.

:: Tester différentes commandes Python
python --version >nul 2>&1
if %errorlevel% == 0 (
    echo ✅ Python trouvé avec la commande 'python'
    echo Installation des packages...
    python -m pip install flask
    python -m pip install flask-cors
    python -m pip install tensorflow
    python -m pip install numpy
    python -m pip install pillow
    goto :end
)

py --version >nul 2>&1
if %errorlevel% == 0 (
    echo ✅ Python trouvé avec la commande 'py'
    echo Installation des packages...
    py -m pip install flask
    py -m pip install flask-cors
    py -m pip install tensorflow
    py -m pip install numpy
    py -m pip install pillow
    goto :end
)

python3 --version >nul 2>&1
if %errorlevel% == 0 (
    echo ✅ Python trouvé avec la commande 'python3'
    echo Installation des packages...
    python3 -m pip install flask
    python3 -m pip install flask-cors
    python3 -m pip install tensorflow
    python3 -m pip install numpy
    python3 -m pip install pillow
    goto :end
)

echo ❌ Python non trouvé!
echo Veuillez installer Python depuis https://www.python.org/downloads/
echo N'oubliez pas de cocher "Add Python to PATH"

:end
echo.
echo Installation terminée!
pause