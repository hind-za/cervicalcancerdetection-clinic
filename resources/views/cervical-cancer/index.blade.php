<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Détection du Cancer Cervical - IA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">
                <i class="fas fa-microscope text-blue-600 mr-3"></i>
                Détection du Cancer Cervical
            </h1>
            <p class="text-gray-600 text-lg">Analyse d'images cytologiques par Intelligence Artificielle</p>
            <div id="api-status" class="mt-4"></div>
        </div>

        <!-- Upload Section -->
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <form id="upload-form" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Sélectionner une image cytologique
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-blue-400 transition-colors">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                                <div class="flex text-sm text-gray-600">
                                    <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                        <span>Télécharger un fichier</span>
                                        <input id="image" name="image" type="file" class="sr-only" accept="image/*" required>
                                    </label>
                                    <p class="pl-1">ou glisser-déposer</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG jusqu'à 10MB</p>
                            </div>
                        </div>
                    </div>

                    <!-- Preview -->
                    <div id="image-preview" class="hidden mb-6">
                        <img id="preview-img" class="max-w-full h-64 object-contain mx-auto rounded-lg shadow-md">
                    </div>

                    <button type="submit" id="analyze-btn" class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-search mr-2"></i>
                        Analyser l'image
                    </button>
                </form>
            </div>
        </div>

        <!-- Loading -->
        <div id="loading" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white p-8 rounded-lg text-center">
                <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600 mx-auto mb-4"></div>
                <p class="text-gray-700">Analyse en cours...</p>
            </div>
        </div>

        <!-- Results -->
        <div id="results" class="hidden max-w-4xl mx-auto mt-8">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">
                    <i class="fas fa-chart-bar text-green-600 mr-2"></i>
                    Résultats de l'analyse
                </h2>
                
                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Image analysée -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Image analysée</h3>
                        <img id="result-image" class="w-full rounded-lg shadow-md">
                        <p id="result-timestamp" class="text-sm text-gray-500 mt-2"></p>
                    </div>

                    <!-- Résultats -->
                    <div>
                        <div class="space-y-6">
                            <!-- Classe prédite -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-gray-700 mb-2">Classe détectée</h4>
                                <p id="predicted-class" class="text-xl font-bold text-blue-600"></p>
                                <p id="confidence" class="text-sm text-gray-600 mt-1"></p>
                            </div>

                            <!-- Niveau de risque -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-gray-700 mb-2">Niveau de risque</h4>
                                <span id="risk-level" class="px-3 py-1 rounded-full text-sm font-medium"></span>
                            </div>

                            <!-- Interprétation -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-gray-700 mb-2">Interprétation</h4>
                                <p id="interpretation" class="text-gray-700"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Toutes les probabilités -->
                <div class="mt-8">
                    <h3 class="text-lg font-semibold mb-4">Détail des probabilités</h3>
                    <div id="all-probabilities" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"></div>
                </div>

                <!-- Avertissement -->
                <div class="mt-8 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle text-yellow-400 mr-3 mt-1"></i>
                        <div>
                            <p class="text-sm text-yellow-700">
                                <strong>Avertissement médical :</strong> Cette analyse est fournie à titre informatif uniquement. 
                                Elle ne remplace pas un diagnostic médical professionnel. Consultez toujours un médecin qualifié 
                                pour une évaluation complète.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-6">
                    <button onclick="resetForm()" class="bg-gray-600 text-white py-2 px-6 rounded-md hover:bg-gray-700 transition-colors">
                        <i class="fas fa-redo mr-2"></i>
                        Nouvelle analyse
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Vérifier le statut de l'API au chargement
        document.addEventListener('DOMContentLoaded', function() {
            checkApiStatus();
        });

        // Gestion de l'upload d'image
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-img').src = e.target.result;
                    document.getElementById('image-preview').classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            }
        });

        // Gestion du formulaire
        document.getElementById('upload-form').addEventListener('submit', function(e) {
            e.preventDefault();
            analyzeImage();
        });

        async function checkApiStatus() {
            try {
                const response = await fetch('/cervical-cancer/api-status');
                const data = await response.json();
                
                const statusDiv = document.getElementById('api-status');
                if (data.api_available) {
                    statusDiv.innerHTML = '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800"><i class="fas fa-check-circle mr-2"></i>API disponible</span>';
                } else {
                    statusDiv.innerHTML = '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800"><i class="fas fa-times-circle mr-2"></i>API non disponible</span>';
                }
            } catch (error) {
                console.error('Erreur vérification API:', error);
            }
        }

        async function analyzeImage() {
            const formData = new FormData();
            const imageFile = document.getElementById('image').files[0];
            
            if (!imageFile) {
                alert('Veuillez sélectionner une image');
                return;
            }

            formData.append('image', imageFile);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            // Afficher le loading
            document.getElementById('loading').classList.remove('hidden');
            document.getElementById('analyze-btn').disabled = true;

            try {
                const response = await fetch('/cervical-cancer/analyze', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    displayResults(result.data);
                } else {
                    alert('Erreur: ' + result.error);
                }
            } catch (error) {
                alert('Erreur lors de l\'analyse: ' + error.message);
            } finally {
                document.getElementById('loading').classList.add('hidden');
                document.getElementById('analyze-btn').disabled = false;
            }
        }

        function displayResults(data) {
            // Afficher l'image
            document.getElementById('result-image').src = data.image_path;
            document.getElementById('result-timestamp').textContent = 'Analysé le ' + data.timestamp;

            // Classe prédite
            document.getElementById('predicted-class').textContent = data.classe_predite;
            document.getElementById('confidence').textContent = `Confiance: ${(data.probabilite * 100).toFixed(1)}%`;

            // Niveau de risque
            const riskElement = document.getElementById('risk-level');
            riskElement.textContent = data.risque;
            
            // Couleurs selon le risque
            riskElement.className = 'px-3 py-1 rounded-full text-sm font-medium ';
            if (data.risque === 'Élevé') {
                riskElement.className += 'bg-red-100 text-red-800';
            } else if (data.risque === 'Modéré') {
                riskElement.className += 'bg-yellow-100 text-yellow-800';
            } else {
                riskElement.className += 'bg-green-100 text-green-800';
            }

            // Interprétation
            document.getElementById('interpretation').textContent = data.interpretation;

            // Toutes les probabilités
            const probsContainer = document.getElementById('all-probabilities');
            probsContainer.innerHTML = '';
            
            Object.entries(data.toutes_probabilites).forEach(([classe, prob]) => {
                const div = document.createElement('div');
                div.className = 'bg-gray-50 p-3 rounded-lg';
                div.innerHTML = `
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">${classe}</span>
                        <span class="text-sm text-gray-600">${(prob * 100).toFixed(1)}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: ${prob * 100}%"></div>
                    </div>
                `;
                probsContainer.appendChild(div);
            });

            // Afficher les résultats
            document.getElementById('results').classList.remove('hidden');
            document.getElementById('results').scrollIntoView({ behavior: 'smooth' });
        }

        function resetForm() {
            document.getElementById('upload-form').reset();
            document.getElementById('image-preview').classList.add('hidden');
            document.getElementById('results').classList.add('hidden');
        }
    </script>
</body>
</html>