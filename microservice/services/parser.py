import re

def parse_surat_text(text: str) -> dict:
    """
    Parses common fields from Indonesian official letters.
    """
    data = {
        "nomor_surat": "",
        "pengirim": "",
        "tanggal_surat": "",
        "perihal": "",
        "hari_acara": "",
        "tanggal_acara": "",
        "waktu_acara": "",
        "tempat_acara": ""
    }
    
    # Simple regex patterns
    nomor_pattern = re.search(r'Nomor\s*:\s*(.+)', text, re.IGNORECASE)
    if nomor_pattern:
        data["nomor_surat"] = nomor_pattern.group(1).strip()
        
    perihal_pattern = re.search(r'(?:Perihal|Hal)\s*:\s*(.+)', text, re.IGNORECASE)
    if perihal_pattern:
        data["perihal"] = perihal_pattern.group(1).strip()
        
    hari_pattern = re.search(r'Hari\s*(?:,|:|/)\s*([A-Za-z]+)', text, re.IGNORECASE)
    if hari_pattern:
        data["hari_acara"] = hari_pattern.group(1).strip()
        
    waktu_pattern = re.search(r'Waktu\s*:\s*(.+)', text, re.IGNORECASE)
    if waktu_pattern:
        data["waktu_acara"] = waktu_pattern.group(1).strip()
        
    tempat_pattern = re.search(r'Tempat\s*:\s*(.+)', text, re.IGNORECASE)
    if tempat_pattern:
        data["tempat_acara"] = tempat_pattern.group(1).strip()
        
    # Heuristic for Pengirim (usually the first few lines, before 'Kepada' or 'Nomor')
    lines = [line.strip() for line in text.split('\n') if line.strip()]
    if lines:
        for i, line in enumerate(lines[:5]):
            if not any(keyword in line.upper() for keyword in ['NOMOR', 'LAMPIRAN', 'PERIHAL', 'KEPADA']):
                if len(line) > 5 and i < 3:
                    data["pengirim"] = line
                    break
                    
    # Check if we found any data at all
    has_data = any(val for val in data.values() if val != "")
    data["_has_data"] = has_data
                    
    return data
