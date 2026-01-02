# Analisis Tab Form Kesehatan Berdasarkan Role

## 📋 Daftar Isi
1. [Role: OPERATOR](#role-operator)
2. [Role: PERAWAT](#role-perawat)
3. [Role: SUPERADMIN](#role-superadmin)
4. [Ringkasan Akses](#ringkasan-akses)

---

## 🔵 ROLE: OPERATOR

### ✅ Bagian yang DAPAT DIAKSES Operator

#### **A. Kunjungan Awal (Status: "Kunjungan Awal")**

**1. Riwayat Penyakit** ✅ (READ-ONLY/DISABLED)
- Checkbox "Tidak Ada Riwayat Penyakit" (disabled)
- Daftar penyakit checkbox (disabled):
  - Diabetes Melitus, Gagal Ginjal, Gagal Jantung, HIV/AIDS, Kusta, Stroke
  - Kanker (dengan dropdown jenis) (disabled)
  - Penyakit Paru (dengan dropdown: TBC, Pneumonia, PPOK) (disabled)

**2. Skrining ILP** ✅ (READ-ONLY/DISABLED)
- Semua skrining dengan radio Ya/Tidak (disabled)
- Status dropdown (Penderita/Bukan Penderita) (disabled)

**3. SKILAS - Lansia Sederhana** ✅ (READ-ONLY/DISABLED)
- Semua field SKILAS (disabled):
  - Penurunan Kognitif
  - Keterbatasan Mobilisasi
  - Malnutrisi (3 pertanyaan)
  - Gangguan Penglihatan
  - Gangguan Pendengaran
  - Gejala Depresi (2 pertanyaan)

**4. Dukungan Keluarga/Pendamping** ✅ (READ-ONLY/DISABLED)
- Dropdown ketersediaan pendamping (disabled)

**5. Permasalahan di Luar Kesehatan** ✅ (READ-ONLY/DISABLED)
- Dropdown status permasalahan (disabled)
- Textarea keterangan (readonly)

**6. Jenis Gangguan Fungsional** ✅ (READ-ONLY/DISABLED)
- 6 gangguan fungsional (radio Ya/Tidak) (disabled)

**7. Perawatan Umum Yang Dilakukan** ✅ (READ-ONLY/DISABLED)
- 9 perawatan umum (radio Ya/Tidak) (disabled)

**8. Perawatan Khusus Yang Dilakukan** ✅ (READ-ONLY/DISABLED)
- 5 perawatan khusus (radio Ya/Tidak) (disabled)

**9. Keluaran dari Perawatan** ✅ (READ-ONLY/DISABLED)
- Radio: Meningkat/Tetap/Menurun (disabled)
- Textarea keterangan (readonly)

**10. Tingkat Kemandirian Keluarga** ✅ (READ-ONLY/DISABLED)
- 7 checkbox kemandirian (disabled)

**11. Hasil Tindakan Keperawatan (Operator)** ✅ (EDITABLE)
- Textarea "hasil tindak keperawatan" (editable)

**12. Pembinaan Keluarga** ✅ (EDITABLE)
- Dropdown: Ya/Tidak (editable)

**13. Rujukan** ✅ (READ-ONLY/DISABLED)
- Radio: Apakah Pasien Perlu Rujukan? (disabled)
- Textarea keterangan rujukan (disabled)

**14. Kunjungan Lanjutan** ✅ (READ-ONLY/DISABLED)
- Dropdown: "apakah akan di lakukan kunjungan lanjutan?" (disabled)
- Checklist "Dilakukan Oleh" (disabled)
- Dropdown pilih operator (disabled)
- Textarea permasalahan lanjutan (readonly)
- Input tanggal kunjungan (readonly)
- Dropdown alasan henti layanan (disabled)

---

#### **B. Kunjungan Lanjutan (Status: "Kunjungan Lanjutan")**

**1. Riwayat Penyakit** ❌ (HIDDEN)
- Section tersembunyi

**2. Skrining ILP** ❌ (HIDDEN)
- Section tersembunyi

**3. SKILAS - Lansia Sederhana** ❌ (HIDDEN)
- Section tersembunyi

**4. Dukungan Keluarga/Pendamping** ❌ (HIDDEN)
- Section tersembunyi

**5. Permasalahan di Luar Kesehatan** ❌ (HIDDEN)
- Section tersembunyi

**6. Jenis Gangguan Fungsional** ❌ (HIDDEN)
- Section tersembunyi

**7. Perawatan Umum Yang Dilakukan** ❌ (HIDDEN)
- Section tersembunyi

**8. Perawatan Khusus Yang Dilakukan** ❌ (HIDDEN)
- Section tersembunyi

**9. Keluaran dari Perawatan** ❌ (HIDDEN)
- Section tersembunyi

**10. Tingkat Kemandirian Keluarga** ❌ (HIDDEN)
- Section tersembunyi

**11. Hasil Tindakan Keperawatan (Operator)** ✅ (EDITABLE)
- Textarea "hasil tindak keperawatan" (editable)

**12. Pembinaan Keluarga** ✅ (EDITABLE)
- Dropdown: Ya/Tidak (editable)

**13. Rujukan** ✅ (EDITABLE)
- Radio: Apakah Pasien Perlu Rujukan? (editable)
- Textarea keterangan rujukan (editable)

**14. Kunjungan Lanjutan** ✅ (EDITABLE)
- Dropdown: "apakah akan di lakukan kunjungan lanjutan?" (editable)
- Checklist "Dilakukan Oleh" (editable)
- Dropdown pilih operator (editable jika checkbox petugas dipilih)
- Textarea permasalahan lanjutan (editable)
- Input tanggal kunjungan (readonly)
- Dropdown alasan henti layanan (editable)

---

## 🟢 ROLE: PERAWAT

### ✅ Bagian yang DAPAT DIAKSES Perawat

#### **A. Kunjungan Awal (Status: "Kunjungan Awal")**

**1. Riwayat Penyakit** ✅ (EDITABLE)
- Checkbox "Tidak Ada Riwayat Penyakit"
- Daftar penyakit checkbox (editable)
- Kanker dengan dropdown jenis (editable)
- Penyakit Paru dengan dropdown (editable)

**2. Skrining ILP** ✅ (EDITABLE)
- Semua skrining dengan radio Ya/Tidak (editable)
- Status dropdown (Penderita/Bukan Penderita) (editable)

**3. SKILAS - Lansia Sederhana** ✅ (EDITABLE)
- Semua field SKILAS (editable)

**4. Dukungan Keluarga/Pendamping** ✅ (EDITABLE)
- Dropdown ketersediaan pendamping (editable)

**5. Permasalahan di Luar Kesehatan** ✅ (EDITABLE)
- Dropdown status permasalahan (editable)
- Textarea keterangan (editable)

**6. Jenis Gangguan Fungsional** ✅ (EDITABLE)
- 6 gangguan fungsional (radio Ya/Tidak) (editable)

**7. Perawatan Umum Yang Dilakukan** ✅ (EDITABLE)
- 9 perawatan umum (radio Ya/Tidak) (editable)

**8. Perawatan Khusus Yang Dilakukan** ✅ (EDITABLE)
- 5 perawatan khusus (radio Ya/Tidak) (editable)

**9. Keluaran dari Perawatan** ✅ (EDITABLE)
- Radio: Meningkat/Tetap/Menurun (editable)
- Textarea keterangan (editable)

**10. Tingkat Kemandirian Keluarga** ✅ (EDITABLE)
- 7 checkbox kemandirian (editable)

**11. Hasil Tindakan Keperawatan (Perawat)** ✅ (EDITABLE)
- Textarea "Hasil Tindakan Keperawatan" (editable)

**12. Pembinaan Keluarga** ✅ (EDITABLE)
- Dropdown: Ya/Tidak (editable)

**13. Rujukan** ✅ (EDITABLE)
- Radio: Apakah Pasien Perlu Rujukan? (editable)
- Textarea keterangan rujukan (editable)

**14. Kunjungan Lanjutan** ✅ (EDITABLE)
- Dropdown: "apakah akan di lakukan kunjungan lanjutan?" (editable)
- Checklist "Dilakukan Oleh" (editable)
- Dropdown pilih operator (editable jika checkbox petugas dipilih)
- Textarea permasalahan lanjutan (editable)
- Input tanggal kunjungan (editable)
- Dropdown alasan henti layanan (editable)

---

#### **B. Kunjungan Lanjutan (Status: "Kunjungan Lanjutan")**

**1. Riwayat Penyakit** ❌ (HIDDEN)
- Section tersembunyi

**2. Skrining ILP** ❌ (HIDDEN)
- Section tersembunyi

**3. SKILAS - Lansia Sederhana** ❌ (HIDDEN)
- Section tersembunyi

**4. Dukungan Keluarga/Pendamping** ❌ (HIDDEN)
- Section tersembunyi

**5. Permasalahan di Luar Kesehatan** ❌ (HIDDEN)
- Section tersembunyi

**6. Jenis Gangguan Fungsional** ❌ (HIDDEN)
- Section tersembunyi

**7. Perawatan Umum Yang Dilakukan** ❌ (HIDDEN)
- Section tersembunyi

**8. Perawatan Khusus Yang Dilakukan** ❌ (HIDDEN)
- Section tersembunyi

**9. Keluaran dari Perawatan** ❌ (HIDDEN)
- Section tersembunyi

**10. Tingkat Kemandirian Keluarga** ❌ (HIDDEN)
- Section tersembunyi

**11. Hasil Tindakan Keperawatan (Perawat)** ✅ (EDITABLE)
- Textarea "Hasil Tindakan Keperawatan" (editable)

**12. Pembinaan Keluarga** ✅ (EDITABLE)
- Dropdown: Ya/Tidak (editable)

**13. Rujukan** ✅ (EDITABLE)
- Radio: Apakah Pasien Perlu Rujukan? (editable)
- Textarea keterangan rujukan (editable)

**14. Kunjungan Lanjutan** ✅ (EDITABLE)
- Dropdown: "apakah akan di lakukan kunjungan lanjutan?" (editable)
- Checklist "Dilakukan Oleh" (editable)
- Dropdown pilih operator (editable jika checkbox petugas dipilih)
- Textarea permasalahan lanjutan (editable)
- Input tanggal kunjungan (editable)
- Dropdown alasan henti layanan (editable)

---

## 🟡 ROLE: SUPERADMIN

### ✅ Bagian yang DAPAT DIAKSES Superadmin

**Semua bagian dapat diakses dan diedit tanpa batasan status kunjungan:**

**1. Riwayat Penyakit** ✅ (EDITABLE)
- Checkbox "Tidak Ada Riwayat Penyakit"
- Daftar penyakit checkbox (editable)
- Kanker dengan dropdown jenis (editable)
- Penyakit Paru dengan dropdown (editable)

**2. Skrining ILP** ✅ (EDITABLE)
- Semua skrining dengan radio Ya/Tidak (editable)
- Status dropdown (Penderita/Bukan Penderita) (editable)

**3. SKILAS - Lansia Sederhana** ✅ (EDITABLE)
- Semua field SKILAS (editable)

**4. Skor AKS** ✅ (EDITABLE - tapi tersembunyi)
- Radio button kategori AKS (editable, tapi section display: none)

**5. Dukungan Keluarga/Pendamping** ✅ (EDITABLE)
- Dropdown ketersediaan pendamping (editable)

**6. Permasalahan di Luar Kesehatan** ✅ (EDITABLE)
- Dropdown status permasalahan (editable)
- Textarea keterangan (editable)

**7. Jenis Gangguan Fungsional** ✅ (EDITABLE)
- 6 gangguan fungsional (radio Ya/Tidak) (editable)

**8. Perawatan Umum Yang Dilakukan** ✅ (EDITABLE)
- 9 perawatan umum (radio Ya/Tidak) (editable)

**9. Perawatan Khusus Yang Dilakukan** ✅ (EDITABLE)
- 5 perawatan khusus (radio Ya/Tidak) (editable)

**10. Keluaran dari Perawatan** ✅ (EDITABLE)
- Radio: Meningkat/Tetap/Menurun (editable)
- Textarea keterangan (editable)

**11. Tingkat Kemandirian Keluarga** ✅ (EDITABLE)
- 7 checkbox kemandirian (editable)

**12. Hasil Tindakan Keperawatan (Perawat)** ✅ (EDITABLE)
- Textarea "Hasil Tindakan Keperawatan" (editable)

**13. Hasil Tindakan Keperawatan (Operator)** ✅ (EDITABLE)
- Textarea "hasil tindak keperawatan" (editable)

**14. Pembinaan Keluarga** ✅ (EDITABLE)
- Dropdown: Ya/Tidak (editable)

**15. Rujukan** ✅ (EDITABLE)
- Radio: Apakah Pasien Perlu Rujukan? (editable)
- Textarea keterangan rujukan (editable)

**16. Kunjungan Lanjutan** ✅ (EDITABLE)
- Dropdown: "apakah akan di lakukan kunjungan lanjutan?" (editable)
- Checklist "Dilakukan Oleh" (editable)
- Dropdown pilih operator (editable jika checkbox petugas dipilih)
- Textarea permasalahan lanjutan (editable)
- Input tanggal kunjungan (editable)
- Dropdown alasan henti layanan (editable)

---

## 📊 RINGKASAN AKSES

### Tabel Perbandingan Akses

| Bagian Form | Operator (Kunjungan Awal) | Operator (Kunjungan Lanjutan) | Perawat (Kunjungan Awal) | Perawat (Kunjungan Lanjutan) | Superadmin |
|------------|---------------------------|------------------------------|--------------------------|------------------------------|------------|
| **Riwayat Penyakit** | ❌ Disabled | ❌ Hidden | ✅ Editable | ❌ Hidden | ✅ Editable |
| **Skrining ILP** | ❌ Disabled | ❌ Hidden | ✅ Editable | ❌ Hidden | ✅ Editable |
| **SKILAS** | ❌ Disabled | ❌ Hidden | ✅ Editable | ❌ Hidden | ✅ Editable |
| **Skor AKS** | ❌ Hidden | ❌ Hidden | ❌ Hidden | ❌ Hidden | ❌ Hidden* |
| **Dukungan Keluarga** | ❌ Disabled | ❌ Hidden | ✅ Editable | ❌ Hidden | ✅ Editable |
| **Permasalahan Luar Kesehatan** | ❌ Disabled | ❌ Hidden | ✅ Editable | ❌ Hidden | ✅ Editable |
| **Gangguan Fungsional** | ❌ Disabled | ❌ Hidden | ✅ Editable | ❌ Hidden | ✅ Editable |
| **Perawatan Umum** | ❌ Disabled | ❌ Hidden | ✅ Editable | ❌ Hidden | ✅ Editable |
| **Perawatan Khusus** | ❌ Disabled | ❌ Hidden | ✅ Editable | ❌ Hidden | ✅ Editable |
| **Keluaran Perawatan** | ❌ Disabled | ❌ Hidden | ✅ Editable | ❌ Hidden | ✅ Editable |
| **Kemandirian Keluarga** | ❌ Disabled | ❌ Hidden | ✅ Editable | ❌ Hidden | ✅ Editable |
| **Hasil Tindakan (Perawat)** | ❌ Tidak ada | ❌ Tidak ada | ✅ Editable | ✅ Editable | ✅ Editable |
| **Hasil Tindakan (Operator)** | ✅ Editable | ✅ Editable | ❌ Tidak ada | ❌ Tidak ada | ✅ Editable |
| **Pembinaan Keluarga** | ✅ Editable | ✅ Editable | ✅ Editable | ✅ Editable | ✅ Editable |
| **Rujukan** | ❌ Disabled | ✅ Editable | ✅ Editable | ✅ Editable | ✅ Editable |
| **Kunjungan Lanjutan** | ❌ Disabled | ✅ Editable | ✅ Editable | ✅ Editable | ✅ Editable |

*Catatan: Skor AKS memiliki `display: none` untuk semua role, tapi superadmin tetap bisa mengakses jika section diaktifkan.

### Keterangan Status:
- ✅ **Editable**: Field dapat diedit
- ❌ **Disabled**: Field terlihat tapi tidak bisa diedit (readonly/disabled)
- ❌ **Hidden**: Section tersembunyi (tidak terlihat)
- ❌ **Tidak ada**: Section tidak muncul untuk role tersebut

---

## 🔍 Catatan Penting

1. **Operator di Kunjungan Awal**: Hanya bisa mengisi "Hasil Tindakan Keperawatan (Operator)" dan "Pembinaan Keluarga". Semua field lain disabled/readonly.

2. **Operator di Kunjungan Lanjutan**: Bisa mengisi bagian akhir form (Rujukan, Kunjungan Lanjutan, dll), tapi tidak bisa melihat/mengisi bagian awal form (Riwayat, Skrining, SKILAS, dll).

3. **Perawat di Kunjungan Awal**: Akses penuh ke semua bagian form.

4. **Perawat di Kunjungan Lanjutan**: Hanya bisa mengisi bagian akhir form, tidak bisa melihat/mengisi bagian awal form.

5. **Superadmin**: Akses penuh ke semua bagian form tanpa batasan status kunjungan.

6. **Skor AKS**: Section ini memiliki `display: none` untuk semua role, kemungkinan belum diaktifkan atau untuk fitur masa depan.

