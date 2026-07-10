from flask import Flask, request, jsonify
from flask_cors import CORS
from services.extractor import extract_text_from_pdf
from services.parser import parse_surat_text
import os

app = Flask(__name__)
CORS(app)

@app.route('/api/health', methods=['GET'])
def health_check():
    return jsonify({"status": "ok", "service": "pdf-extractor"}), 200

@app.route('/api/extract', methods=['POST'])
def extract_pdf():
    if 'file' not in request.files:
        return jsonify({"success": False, "message": "No file part"}), 400
        
    file = request.files['file']
    if file.filename == '':
        return jsonify({"success": False, "message": "No selected file"}), 400
        
    if file and file.filename.endswith('.pdf'):
        try:
            # Save file temporarily
            temp_path = os.path.join('/tmp', file.filename) if os.name != 'nt' else os.path.join(os.environ.get('TEMP', 'C:\\temp'), file.filename)
            file.save(temp_path)
            
            # Extract text
            text = extract_text_from_pdf(temp_path)
            
            if not text.strip():
                if os.path.exists(temp_path):
                    os.remove(temp_path)
                return jsonify({
                    "success": False,
                    "message": "PDF berupa gambar/scan. Teks tidak dapat dibaca otomatis."
                })
                
            # Parse text
            data = parse_surat_text(text)
            
            # Remove temp file
            if os.path.exists(temp_path):
                os.remove(temp_path)
                
            has_data = data.pop("_has_data", False)
            if not has_data:
                return jsonify({
                    "success": False,
                    "message": "Teks berhasil dibaca, namun format surat tidak standar (sulit diekstrak otomatis)."
                })
                
            return jsonify({
                "success": True,
                "data": data
            })
            
        except Exception as e:
            return jsonify({
                "success": False,
                "message": f"Error processing PDF: {str(e)}"
            }), 500
            
    return jsonify({"success": False, "message": "Invalid file format, must be PDF"}), 400

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)
