from flask import Flask, request, jsonify
from flask_cors import CORS
import tensorflow as tf
import numpy as np
from PIL import Image
import io
import os

app = Flask(__name__)
CORS(app)  # Permettre les requêtes cross-origin depuis Laravel

# Charger le modèle
model_path = "mon_modele.h5"
if os.path.exists(model_path):
    model = tf.keras.models.load_model(model_path)
    print("Modèle chargé avec succès")
else:
    print(f"Erreur: Le fichier {model_path} n'existe pas")
    model = None

CLASSES = ["Dyskeratotic", "Koilocytotic", "Metaplastic", "Parabasal", "Superficial-Intermediate"]

def preprocess(image_bytes):
    """Préprocesser l'image pour la prédiction"""
    try:
        image = Image.open(io.BytesIO(image_bytes)).convert("RGB")
        image = image.resize((224, 224))
        image = np.array(image) / 255.0
        image = np.expand_dims(image, axis=0)
        return image
    except Exception as e:
        raise ValueError(f"Erreur lors du préprocessing: {str(e)}")

@app.route("/", methods=["GET"])
def health_check():
    """Vérifier que l'API fonctionne"""
    return jsonify({
        "status": "OK",
        "message": "API de détection du cancer cervical",
        "model_loaded": model is not None
    })

@app.route("/predict", methods=["POST"])
def predict():
    """Prédire la classe d'une image"""
    if model is None:
        return jsonify({"error": "Modèle non chargé"}), 500
    
    if "image" not in request.files:
        return jsonify({"error": "Aucune image fournie"}), 400
    
    file = request.files["image"]
    if file.filename == "":
        return jsonify({"error": "Aucun fichier sélectionné"}), 400
    
    try:
        image_bytes = file.read()
        img = preprocess(image_bytes)
        
        # Prédiction
        preds = model.predict(img)
        idx = int(np.argmax(preds))
        conf = float(np.max(preds))
        
        # Toutes les probabilités
        all_probs = {
            CLASSES[i]: round(float(preds[0][i]), 4) 
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
    app.run(host="0.0.0.0", port=5000, debug=True)