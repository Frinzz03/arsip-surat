# pyrefly: ignore [missing-import]
import pytest
from services.parser import parse_surat_text

def test_parse_surat_text():
    text = """
    PEMERINTAH KOTA CONTOH
    DINAS PENDIDIKAN
    
    Nomor : 123/DISDIK/2026
    Lampiran : 1 Berkas
    Perihal : Undangan Rapat Koordinasi
    
    Kepada Yth.
    Kepala Sekolah Dasar
    
    Sehubungan dengan acara tahunan, kami mengundang bapak/ibu pada:
    Hari : Senin
    Tanggal : 20 Juli 2026
    Waktu : 09.00 WIB
    Tempat : Aula Dinas Pendidikan
    """
    
    data = parse_surat_text(text)
    
    assert data["nomor_surat"] == "123/DISDIK/2026"
    assert data["perihal"] == "Undangan Rapat Koordinasi"
    assert data["hari_acara"] == "Senin"
    assert data["waktu_acara"] == "09.00 WIB"
    assert data["tempat_acara"] == "Aula Dinas Pendidikan"
    assert data["pengirim"] == "PEMERINTAH KOTA CONTOH"
