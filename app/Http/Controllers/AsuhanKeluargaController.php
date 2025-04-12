<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KondisiRumah;
use App\Models\PhbsRumahTangga;
use App\Models\PemeliharaanKesehatanKeluarga;
use App\Models\PengkajianIndividu;
use App\Models\SirkulasiCairan;
use App\Models\Perkemihan;
use App\Models\Pencernaan;
use App\Models\Muskuloskeletal;
use App\Models\Neurosensori;

class AsuhanKeluargaController extends Controller
{
    public function saveKondisiRumah(Request $request)
    {

        $data = $request->validate([
            'pasien_id' => 'required|uuid',
            'ventilasi' => 'nullable',
            'pencahayaan' => 'nullable',
            'saluran_limbah' => 'nullable',
            'sumber_air' => 'nullable',
            'jamban' => 'nullable',
            'tempat_sampah' => 'nullable',
        ]);

        KondisiRumah::create($data);

        return response()->json(['message' => 'Data berhasil disimpan'], 200);
    }

    public function updateKondisiRumah(Request $request, $id)
    {
        $data = $request->validate([
            'ventilasi' => 'nullable',
            'pencahayaan' => 'nullable',
            'saluran_limbah' => 'nullable',
            'sumber_air' => 'nullable',
            'jamban' => 'nullable',
            'tempat_sampah' => 'nullable',
        ]);

        $kondisiRumah = KondisiRumah::findOrFail($id);
        $kondisiRumah->update($data);

        return response()->json(['message' => 'Data berhasil diupdate'], 200);
    }

    public function savePhbsRumahTangga(Request $request)
    {

        $data = $request->validate([
            'pasien_id' => 'required|uuid',
            'ibu_nifas' => 'nullable',
            'ada_bayi' => 'nullable',
            'ada_balita' => 'nullable',
            'air_bersih'    => 'nullable',
            'mencuci_tangan' => 'nullable',
            'buang_sampah' => 'nullable',
            'menjaga_lingkungan_rumah' => 'nullable',
            'konsumsi_lauk' => 'nullable',
            'gunakan_jamban' => 'nullable',
            'jentik_dirumah' => 'nullable',
            'makan_buah_sayur' => 'nullable',
            'aktivitas_fisik' => 'nullable',
            'merokok_dalam_rumah' => 'nullable',
        ]);

        PhbsRumahTangga::create($data);

        return response()->json(['message' => 'Data berhasil disimpan'], 200);
    }

    public function updatePhbsRumahTangga(Request $request, $id)
    {
        $data = $request->validate([
            'ibu_nifas' => 'nullable',
            'ada_bayi' => 'nullable',
            'ada_balita' => 'nullable',
            'air_bersih'    => 'nullable',
            'mencuci_tangan' => 'nullable',
            'buang_sampah' => 'nullable',
            'menjaga_lingkungan_rumah' => 'nullable',
            'konsumsi_lauk' => 'nullable',
            'gunakan_jamban' => 'nullable',
            'jentik_dirumah' => 'nullable',
            'makan_buah_sayur' => 'nullable',
            'aktivitas_fisik' => 'nullable',
            'merokok_dalam_rumah' => 'nullable',
        ]);

        $phbsRumahTangga = PhbsRumahTangga::findOrFail($id);
        $phbsRumahTangga->update($data);

        return response()->json(['message' => 'Data berhasil diupdate'], 200);
    }

    public function savePemeliharaanKesehatanKeluarga(Request $request)
    {

        $data = $request->validate([
            'pasien_id' => 'required|uuid',
            'perhatian_keluarga' => 'nullable',
            'mengetahui_masalah_kesehatan' => 'nullable',
            'penyebab_masalah_kesehatan' => 'nullable',
            'akibat_masalah_kesehatan' => 'nullable',
            'keyakinan_keluarga' => 'nullable',
            'upaya_peningkatan_kesehatan' => 'nullable',
            'upaya_peningkatan_kesehatan_deskripsi' => 'nullable',
            'kebutuhan_pengobatan' => 'nullable',
            'merawat_anggota_keluarga' => 'nullable',
            'melakukan_pencegahan_masalah' => 'nullable',
            'mendukung_kesehatan' => 'nullable',
            'memanfaatkan_sumber' => 'nullable',
        ]);

        PemeliharaanKesehatanKeluarga::create($data);

        return response()->json(['message' => 'Data berhasil disimpan'], 200);
    }

    public function updatePemeliharaanKesehatanKeluarga(Request $request, $id)
    {
        $data = $request->validate([
            'perhatian_keluarga' => 'nullable',
            'mengetahui_masalah_kesehatan' => 'nullable',
            'penyebab_masalah_kesehatan' => 'nullable',
            'akibat_masalah_kesehatan' => 'nullable',
            'keyakinan_keluarga' => 'nullable',
            'upaya_peningkatan_kesehatan' => 'nullable',
            'upaya_peningkatan_kesehatan_deskripsi' => 'nullable',
            'kebutuhan_pengobatan' => 'nullable',
            'merawat_anggota_keluarga' => 'nullable',
            'melakukan_pencegahan_masalah' => 'nullable',
            'mendukung_kesehatan' => 'nullable',
            'memanfaatkan_sumber' => 'nullable',
        ]);

        $pemeliharaanKesehatanKeluarga = PemeliharaanKesehatanKeluarga::findOrFail($id);
        $pemeliharaanKesehatanKeluarga->update($data);

        return response()->json(['message' => 'Data berhasil diupdate'], 200);
    }

    public function savePengkajianIndividu(Request $request)
    {

        $data = $request->validate([
            'pasien_id' => 'required|uuid',
            'kesadaran' => 'nullable',
            'gcs' => 'nullable',
            'sistole' => 'nullable',
            'diastole' => 'nullable',
            'pernapasan' => 'nullable',
            'suhu' => 'nullable',
            'nadi' => 'nullable',
            'takikardi' => 'nullable',
            'bradikardia' => 'nullable',
            'tubuhHangat' => 'nullable',
            'menggigil' => 'nullable',
        ]);

        PengkajianIndividu::create($data);

        return response()->json(['message' => 'Data berhasil disimpan'], 200);
    }

    public function updatePengkajianIndividu(Request $request, $id)
    {
        $data = $request->validate([
            'pasien_id' => 'required|uuid',
            'kesadaran' => 'nullable',
            'gcs' => 'nullable',
            'sistole' => 'nullable',
            'diastole' => 'nullable',
            'pernapasan' => 'nullable',
            'suhu' => 'nullable',
            'nadi' => 'nullable',
            'takikardi' => 'nullable',
            'bradikardia' => 'nullable',
            'tubuhHangat' => 'nullable',
            'menggigil' => 'nullable',
        ]);

        $pengkajianIndividu = PengkajianIndividu::findOrFail($id);
        $pengkajianIndividu->update($data);

        return response()->json(['message' => 'Data berhasil diupdate'], 200);
    }

    public function saveSirkulasiCairan(Request $request)
    {

        $data = $request->validate([
            'pasien_id' => 'required|uuid',
            'edema' => 'nullable',
            'bunyi_jantung' => 'nullable',
            'asites' => 'nullable',
            'akral_dingin' => 'nullable',
            'tanda_perdarahan' => 'nullable',
            'tanda_anemia' => 'nullable',
            'tanda_dehidrasi' => 'nullable',
            'pusing' => 'nullable',
            'kesemutan' => 'nullable',
            'berkeringat' => 'nullable',
            'rasa_haus' => 'nullable',
            'pengisian_kapiler' => 'nullable',
        ]);

        SirkulasiCairan::create($data);

        return response()->json(['message' => 'Data berhasil disimpan'], 200);
    }

    public function updateSirkulasiCairan(Request $request, $id)
    {
        $data = $request->validate([
            'pasien_id' => 'required|uuid',
            'edema' => 'nullable',
            'bunyi_jantung' => 'nullable',
            'asites' => 'nullable',
            'akral_dingin' => 'nullable',
            'tanda_perdarahan' => 'nullable',
            'tanda_anemia' => 'nullable',
            'tanda_dehidrasi' => 'nullable',
            'pusing' => 'nullable',
            'kesemutan' => 'nullable',
            'berkeringat' => 'nullable',
            'rasa_haus' => 'nullable',
            'pengisian_kapiler' => 'nullable',
        ]);

        $sirkulasiCairan = SirkulasiCairan::findOrFail($id);
        $sirkulasiCairan->update($data);

        return response()->json(['message' => 'Data berhasil diupdate'], 200);
    }

    public function savePerkemihan(Request $request)
    {
        $data = $request->validate([
            'pasien_id' => 'required|uuid',
            'pola_bak' => 'nullable',
            'volume' => 'nullable',
            'hematuri' => 'nullable',
            'poliuria' => 'nullable',
            'oliguria' => 'nullable',
            'disuria' => 'nullable',
            'inkontinensia' => 'nullable',
            'retensi' => 'nullable',
            'nyeri_bak' => 'nullable',
            'kemampuan_bak' => 'nullable',
            'alat_bantu_bak' => 'nullable',
            'obat_bak' => 'nullable',
            'kemampuan_bab' => 'nullable',
            'alat_bantu_bab' => 'nullable',
            'obat_bab' => 'nullable',
        ]);

        Perkemihan::create($data);

        return response()->json(['message' => 'Data berhasil disimpan'], 200);
    }

    public function updatePerkemihan(Request $request, $id)
    {
        $data = $request->validate([
            'pasien_id' => 'required|uuid',
            'pola_bak' => 'nullable',
            'volume' => 'nullable',
            'hematuri' => 'nullable',
            'poliuria' => 'nullable',
            'oliguria' => 'nullable',
            'disuria' => 'nullable',
            'inkontinensia' => 'nullable',
            'retensi' => 'nullable',
            'nyeri_bak' => 'nullable',
            'kemampuan_bak' => 'nullable',
            'alat_bantu_bak' => 'nullable',
            'obat_bak' => 'nullable',
            'kemampuan_bab' => 'nullable',
            'alat_bantu_bab' => 'nullable',
            'obat_bab' => 'nullable',
        ]);

        $perkemihan = Perkemihan::findOrFail($id);
        $perkemihan->update($data);

        return response()->json(['message' => 'Data berhasil diupdate'], 200);
    }

    public function savePencernaan(Request $request)
    {
        $data = $request->validate([
            'pasien_id' => 'required|uuid',
            'mual' => 'nullable',
            'muntah' => 'nullable',
            'kembung' => 'nullable',
            'nafsu_makan' => 'nullable',
            'sulit_menelan' => 'nullable',
            'disfagia' => 'nullable',
            'bau_napas' => 'nullable',
            'kerusakan_gigi' => 'nullable',
            'distensi_abdomen' => 'nullable',
            'bising_usus' => 'nullable',
            'konstipasi' => 'nullable',
            'diare' => 'nullable',
            'hemoroid' => 'nullable',
            'stomatitis' => 'nullable',
            'warna_stomatitis' => 'nullable',
            'massa_abdomen' => 'nullable',
            'obat_pencahar' => 'nullable',
            'konsistensi' => 'nullable',
            'diet_khusus' => 'nullable',
            'kebiasaan_makan' => 'nullable',
            'alergi_makanan' => 'nullable',
            'alat_bantu' => 'nullable',
        ]);

        Pencernaan::create($data);

        return response()->json(['message' => 'Data berhasil disimpan'], 200);
    }

    public function updatePencernaan(Request $request, $id)
    {
        $data = $request->validate([
            'pasien_id' => 'required|uuid',
            'mual' => 'nullable',
            'muntah' => 'nullable',
            'kembung' => 'nullable',
            'nafsu_makan' => 'nullable',
            'sulit_menelan' => 'nullable',
            'disfagia' => 'nullable',
            'bau_napas' => 'nullable',
            'kerusakan_gigi' => 'nullable',
            'distensi_abdomen' => 'nullable',
            'bising_usus' => 'nullable',
            'konstipasi' => 'nullable',
            'diare' => 'nullable',
            'hemoroid' => 'nullable',
            'stomatitis' => 'nullable',
            'warna_stomatitis' => 'nullable',
            'massa_abdomen' => 'nullable',
            'obat_pencahar' => 'nullable',
            'konsistensi' => 'nullable',
            'diet_khusus' => 'nullable',
            'kebiasaan_makan' => 'nullable',
            'alergi_makanan' => 'nullable',
            'alat_bantu' => 'nullable',
        ]);

        $pencernaan = Pencernaan::findOrFail($id);
        $pencernaan->update($data);

        return response()->json(['message' => 'Data berhasil diupdate'], 200);
    }

    public function saveMuskuloskeletal(Request $request)
    {
        $data = $request->validate([
            'pasien_id' => 'required|uuid',
            'kontraktur' => 'nullable',
            'fraktur' => 'nullable',
            'nyeri_otot_tulang' => 'nullable',
            'drop_foot_lokasi' => 'nullable',
            'tremor' => 'nullable',
            'malaise_fatigue' => 'nullable',
            'atrofi' => 'nullable',
            'kekuatan_otot' => 'nullable',
            'postur_tidak_normal' => 'nullable',
            'alat_bantu' => 'nullable',
            'nyeri' => 'nullable',
            'tonus_otot' => 'nullable',
            'ekstremitas_atas' => 'nullable',
            'berdiri' => 'nullable',
            'berjalan' => 'nullable',
        ]);

        Muskuloskeletal::create($data);

        return response()->json(['message' => 'Data berhasil disimpan'], 200);
    }

    public function updateMuskuloskeletal(Request $request, $id)
    {
        $data = $request->validate([
            'pasien_id' => 'required|uuid',
            'kontraktur' => 'nullable',
            'fraktur' => 'nullable',
            'nyeri_otot_tulang' => 'nullable',
            'drop_foot_lokasi' => 'nullable',
            'tremor' => 'nullable',
            'malaise_fatigue' => 'nullable',
            'atrofi' => 'nullable',
            'kekuatan_otot' => 'nullable',
            'postur_tidak_normal' => 'nullable',
            'alat_bantu' => 'nullable',
            'nyeri' => 'nullable',
            'tonus_otot' => 'nullable',
            'ekstremitas_atas' => 'nullable',
            'berdiri' => 'nullable',
            'berjalan' => 'nullable',
        ]);

        $muskuloskeletal = Muskuloskeletal::findOrFail($id);
        $muskuloskeletal->update($data);

        return response()->json(['message' => 'Data berhasil diupdate'], 200);
    }

    public function saveNeurosensori(Request $request)
    {
        $data = $request->validate([
            'pasien_id' => 'required|uuid',
            'buram' => 'nullable',
            'tidak_bisa_melihat' => 'nullable',
            'alat_bantu_penglihatan' => 'nullable',
            'visus' => 'nullable',
            'kesemutan' => 'nullable',
            'kebas' => 'nullable',
            'kurang_jelas' => 'nullable',
            'tuli' => 'nullable',
            'tinnitus' => 'nullable',
            'refleks_patologi' => 'nullable',
            'disorientasi' => 'nullable',
            'parese' => 'nullable',
            'alat_bantu_saraf' => 'nullable',
            'halusinasi' => 'nullable',
            'disatria' => 'nullable',
            'amnesia' => 'nullable',
            'kekuatan_otot' => 'nullable',
            'postur_tidak_normal' => 'nullable',
            'nyeri' => 'nullable',
            'sifat' => 'nullable',
            'frekuensi' => 'nullable',
            'lama' => 'nullable',
            'mampu' => 'nullable',
            'terganggu' => 'nullable',
            'memar' => 'nullable',
            'laserasi' => 'nullable',
            'ulserasi' => 'nullable',
            'pus' => 'nullable',
            'bulae_lepuh' => 'nullable',
            'perdarahan_bawah' => 'nullable',
            'krusta' => 'nullable',
            'perubahan_warna' => 'nullable',
            'luka_bakar_kulit' => 'nullable',
            'decubitus_grade' => 'nullable',
            'decubitus_lokasi' => 'nullable',
            'susah_tidur' => 'nullable',
            'waktu_tidur' => 'nullable',
            'bantuan_obat' => 'nullable',
            'cemas' => 'nullable',
            'marah' => 'nullable',
            'denial' => 'nullable',
            'takut' => 'nullable',
            'putus_asa' => 'nullable',
            'depresi' => 'nullable',
            'rendah_diri' => 'nullable',
            'menarik_diri' => 'nullable',
            'agresif' => 'nullable',
            'perilaku_kekerasan' => 'nullable',
            'tidak_mau_melihat_bagian_tubuh_yang_rusak' => 'nullable',
            'respon_pasca_trauma' => 'nullable',
            'interaksi_keluarga' => 'nullable',
            'berkomunikasi' => 'nullable',
            'kegiatan_sosial' => 'nullable',
            'gigi_dan_mulut_kotor' => 'nullable',
            'kulit_kotor' => 'nullable',
            'hidung_kotor' => 'nullable',
            'telinga_kotor' => 'nullable',
            'mata_kotor' => 'nullable',
            'perial_genial_kotor' => 'nullable',
            'kuku_kotor' => 'nullable',
            'rambut_kepala_kotor' => 'nullable',
            'mandi' => 'nullable',
            'berpakaian' => 'nullable',
            'menyisir_rambut' => 'nullable',
        ]);

        Neurosensori::create($data);

        return response()->json(['message' => 'Data berhasil disimpan'], 200);
    }

    public function updateNeurosensori(Request $request, $id)
    {
        $data = $request->validate([
            'pasien_id' => 'required|uuid',
            'buram' => 'nullable',
            'tidak_bisa_melihat' => 'nullable',
            'alat_bantu_penglihatan' => 'nullable',
            'visus' => 'nullable',
            'kesemutan' => 'nullable',
            'kebas' => 'nullable',
            'kurang_jelas' => 'nullable',
            'tuli' => 'nullable',
            'tinnitus' => 'nullable',
            'refleks_patologi' => 'nullable',
            'disorientasi' => 'nullable',
            'parese' => 'nullable',
            'alat_bantu_saraf' => 'nullable',
            'halusinasi' => 'nullable',
            'disatria' => 'nullable',
            'amnesia' => 'nullable',
            'kekuatan_otot' => 'nullable',
            'postur_tidak_normal' => 'nullable',
            'nyeri' => 'nullable',
            'sifat' => 'nullable',
            'frekuensi' => 'nullable',
            'lama' => 'nullable',
            'mampu' => 'nullable',
            'terganggu' => 'nullable',
            'memar' => 'nullable',
            'laserasi' => 'nullable',
            'ulserasi' => 'nullable',
            'pus' => 'nullable',
            'bulae_lepuh' => 'nullable',
            'perdarahan_bawah' => 'nullable',
            'krusta' => 'nullable',
            'perubahan_warna' => 'nullable',
            'luka_bakar_kulit' => 'nullable',
            'decubitus_grade' => 'nullable',
            'decubitus_lokasi' => 'nullable',
            'susah_tidur' => 'nullable',
            'waktu_tidur' => 'nullable',
            'bantuan_obat' => 'nullable',
            'cemas' => 'nullable',
            'marah' => 'nullable',
            'denial' => 'nullable',
            'takut' => 'nullable',
            'putus_asa' => 'nullable',
            'depresi' => 'nullable',
            'rendah_diri' => 'nullable',
            'menarik_diri' => 'nullable',
            'agresif' => 'nullable',
            'perilaku_kekerasan' => 'nullable',
            'tidak_mau_melihat_bagian_tubuh_yang_rusak' => 'nullable',
            'respon_pasca_trauma' => 'nullable',
            'interaksi_keluarga' => 'nullable',
            'berkomunikasi' => 'nullable',
            'kegiatan_sosial' => 'nullable',
            'gigi_dan_mulut_kotor' => 'nullable',
            'kulit_kotor' => 'nullable',
            'hidung_kotor' => 'nullable',
            'telinga_kotor' => 'nullable',
            'mata_kotor' => 'nullable',
            'perial_genial_kotor' => 'nullable',
            'kuku_kotor' => 'nullable',
            'rambut_kepala_kotor' => 'nullable',
            'mandi' => 'nullable',
            'berpakaian' => 'nullable',
            'menyisir_rambut' => 'nullable',
        ]);

        $neurosensori = Neurosensori::findOrFail($id);
        $neurosensori->update($data);

        return response()->json(['message' => 'Data berhasil diperbarui'], 200);
    }
}
