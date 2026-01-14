from flask import Flask, request, jsonify
from flask_cors import CORS
import random
import time

app = Flask(__name__)
CORS(app)

# Classes simulées
CLASSES = ["Dyskeratotic", "Koilocytotic", "Metaplastic", "Parabasal", "Superficial-Intermediate"]

@app.route("/", methods=["GET"])
def health_check():
    """Vérifier que l'API fonctionne"""
    return jsonify({
        "status": "OK",
        "message": "API de détection du cancer cervical (MODE TEST)",
        "model_loaded": True
    })

@app.route("/predict", methods=["POST"])
def predict():
    """Prédiction simulée pour les tests"""
    if "image" not in request.files:
        return jsonify({"error": "Aucune image fournie"}), 400
    
    file = request.files["image"]
    if file.filename == "":
        return jsonify({"error": "Aucun fichier sélectionné"}), 400
    
    try:
        # Simulation d'une prédiction (remplace le vrai modèle)
        time.sleep(2)  # Simuler le temps de traitement
        
        # Générer des probabilités aléatoires
        probs = [random.random() for _ in range(len(CLASSES))]
        total = sum(probs)
        probs = [p/total for p in probs]  # Normaliser
        
        # Trouver la classe avec la plus haute probabilité
        idx = probs.index(max(probs))
        conf = max(probs)
        
        # Toutes les probabilités
        all_probs = {
            CLASSES[i]: round(probs[i], 4) 
            for i in range(len(CLASSES))
        }
        
        return jsonify({
            "success": True,
            "classe_predite": CLASSES[idx],
            "probabilite": round(conf, 4),
            "toutes_probabilites": all_probs,
            "risque": "Élevé" if conf > 0.8 else "Modéré" if conf > 0.6 else "Faible"
        })
        
    except Exception as e:
        return jsonify({
            "success": False,
            "error": f"Erreur lors de la prédiction: {str(e)}"
        }), 500

if __name__ == "__main__":
    print("🧪 MODE TEST - API Flask sans TensorFlow")
    print("Cette version simule les prédictions pour tester l'intégration")
    print("Pour utiliser le vrai modèle, installez TensorFlow et utilisez app.py")
    print("-" * 60)
    app.run(host="0.0.0.0", port=5000, debug=True)