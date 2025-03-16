@extends('layouts.app')

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Pasien</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-sm-6">
                                    <h5 class="card-title">ACTIVITY OF DAILY LIVING</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('users.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="name">Mengendalikan rangsangan BAB</label>
                                                    <select class="form-control mt-2 mb-2 bab" name="bab1" required>
                                                        <option value="">-- Pilih --</option>
                                                        <option value="0">Tidak terkendali/ tak teratur (perlu
                                                            pencahar)</option>
                                                        <option value="1">Kadang - kadang tak terkendali (1x/ minggu)
                                                        </option>
                                                        <option value="2">Terkendali teratur</option>
                                                    </select>
                                                    @error('bab')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="name">Mengendalikan rangsangan BAK</label>
                                                        <select class="form-control mt-2 mb-2 bab" name="bab10" required>
                                                            <option value="">-- Pilih --</option>
                                                            <option value="0">Tak terkendali atau pakai kateter
                                                            </option>
                                                            <option value="1">Kadang-kadang tek terkendali ( hanya 1x/
                                                                24 jam)</option>
                                                            <option value="2">Mandiri </option>
                                                        </select>
                                                        @error('bab')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="name">Makan minum (jika makan harus berupa
                                                            potongan, dianggap dibantu)</label>
                                                        <select class="form-control mt-2 mb-2 bab" name="bab2" required>
                                                            <option value="">-- Pilih --</option>
                                                            <option value="0">Tidak mampu</option>
                                                            <option value="1">Perlu ditolong memotong makanan</option>
                                                            <option value="2">Mandiri </option>
                                                        </select>
                                                        @error('bab')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="name">Naik turun tangga</label>
                                                        <select class="form-control mt-2 mb-2 bab" name="bab3" required>
                                                            <option value="">-- Pilih --</option>
                                                            <option value="0">Tidak mampu</option>
                                                            <option value="1">Butuh pertolongan</option>
                                                            <option value="2">Mandiri</option>
                                                        </select>
                                                        @error('bab')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="name">Mandi</label>
                                                        <select class="form-control mt-2 mb-2 bab" name="bab4" required>
                                                            <option value="">-- Pilih --</option>
                                                            <option value="0">Tergantung orang lain</option>
                                                            <option value="1">Mandiri</option>
                                                        </select>
                                                        @error('bab')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                  <div class="form-group">
                                                      <label for="name">Bergerak dari kursi roda ke tempat tidur dan
                                                          sebaliknya (termasuk duduk di tempat tidur)</label>
                                                      <select class="form-control mt-2 mb-2 bab" name="bab5" required>
                                                          <option value="">-- Pilih --</option>
                                                          <option value="0">Tidak mampu</option>
                                                          <option value="1">Perlu banyak bantuan untuk bisa duduk (2
                                                              orang)
                                                          </option>
                                                          <option value="2">Bantuan minimal 1 orang</option>
                                                      </select>
                                                      @error('bab')
                                                          <span class="invalid-feedback" role="alert">
                                                              <strong>{{ $message }}</strong>
                                                          </span>
                                                      @enderror
                                                  </div>
                                              </div>
                                              <div class="col-sm-12">
                                                  <div class="form-group">
                                                      <label for="name">Berjalan di tempat rata (atau jika tidak bisa
                                                          berjalan, menjalankan kursi roda)</label>
                                                      <select class="form-control mt-2 mb-2 bab" name="bab6" required>
                                                          <option value="">-- Pilih --</option>
                                                          <option value="0">Tidak mampu</option>
                                                          <option value="1">Bisa (pindah) dengan kursi roda</option>
                                                          <option value="2">Berjalan dengan bantuan orang lain</option>
                                                          <option value="3">Mandiri</option>
                                                      </select>
                                                      @error('bab')
                                                          <span class="invalid-feedback" role="alert">
                                                              <strong>{{ $message }}</strong>
                                                          </span>
                                                      @enderror
                                                  </div>
                                              </div>
                                              <div class="col-sm-12">
                                                  <div class="form-group">
                                                      <label for="name">Berpakaian (termasuk memasang tali sepatu,
                                                          mengencangkan sabuk)</label>
                                                      <select class="form-control mt-2 mb-2 bab" name="bab7" required>
                                                          <option value="">-- Pilih --</option>
                                                          <option value="0">Tergantung orang lain</option>
                                                          <option value="1">Sebagian dibantu (mis: mengancing baju)
                                                          </option>
                                                          <option value="2">Mandiri</option>
                                                      </select>
                                                      @error('bab')
                                                          <span class="invalid-feedback" role="alert">
                                                              <strong>{{ $message }}</strong>
                                                          </span>
                                                      @enderror
                                                  </div>
                                              </div>
                                              <div class="col-sm-12">
                                                  <div class="form-group">
                                                      <label for="name">Membersihkan diri (mencuci wajah, menyikat
                                                          rambut, mencukur kumis, sikat gigi)</label>
                                                      <select class="form-control mt-2 mb-2 bab" name="bab8" required>
                                                          <option value="">-- Pilih --</option>
                                                          <option value="1">Butuh pertolongan orang lain</option>
                                                          <option value="2">Mandiri</option>
                                                      </select>
                                                      @error('bab')
                                                          <span class="invalid-feedback" role="alert">
                                                              <strong>{{ $message }}</strong>
                                                          </span>
                                                      @enderror
                                                  </div>
                                              </div>
                                              <div class="col-sm-12">
                                                  <div class="form-group">
                                                      <label for="name">Penggunaan WC (keluar masuk WC,
                                                          melepas/memakai celana, cebok, menyiram)</label>
                                                      <select class="form-control mt-2 mb-2 bab" name="bab9" required>
                                                          <option value="">-- Pilih --</option>
                                                          <option value="0">Tergantung pertolongan orang lain
                                                          </option>
                                                          <option value="1">Perlu pertolongan pada beberapa kegiatan
                                                              tetapi dapat mengerjakan sendiri beberapa kegiatan yang lain
                                                          </option>
                                                          <option value="2">Mandiri</option>
                                                      </select>
                                                      @error('bab')
                                                          <span class="invalid-feedback" role="alert">
                                                              <strong>{{ $message }}</strong>
                                                          </span>
                                                      @enderror
                                                  </div>
                                              </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="card">
                                             <div class="container">
                                                 <div class="card-body">
                                                     <p class="text-center h6 fw-bold">Skor Modifikasi Barthel Indeks</p>
                                                     <table class="table">
                                                         <tbody>
                                                             <tr>
                                                                 <td class="font-weight-bold">20</td>
                                                                 <td>: Mandiri (A)</td>
                                                             </tr>
                                                             <tr>
                                                                 <td class="font-weight-bold">12 - 19</td>
                                                                 <td>: Ketergantungan Ringan (B)</td>
                                                             </tr>
                                                             <tr>
                                                                 <td class="font-weight-bold">9 - 11</td>
                                                                 <td>: Ketergantungan Sedang (B)</td>
                                                             </tr>
                                                             <tr>
                                                                 <td class="font-weight-bold">5 - 8</td>
                                                                 <td>: Ketergantungan Berat (C)</td>
                                                             </tr>
                                                             <tr>
                                                                 <td class="font-weight-bold">0 - 4</td>
                                                                 <td>: Ketergantungan Total (C)</td>
                                                             </tr>
                                                         </tbody>
                                                     </table>
                                                 </div>
                                             </div>
                                         </div>
                                              
                                         
                                        <div class="card text-center mt-2">
                                            <div class="card-header h5 fw-bold">
                                                Total Skor
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text h2 fw-bold"  id="total-score">0</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 mt-3">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                        <a href="{{ route('users.index') }}" class="btn btn-secondary">Kembali</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
     document.addEventListener('DOMContentLoaded', function() {
     // Ambil semua dropdown dengan class 'bab'
     const babSelectors = document.querySelectorAll('.bab');

     // Fungsi untuk menghitung skor total
     function calculateScore() {
          let totalScore = 0;

          babSelectors.forEach(select => {
               const value = parseInt(select.value);
               if (!isNaN(value)) {
                    totalScore += value;
               }
          });

          // Menampilkan skor total di elemen dengan id 'total-score'
          document.getElementById('total-score').innerText = totalScore;
     }

     // Menambahkan event listener pada setiap dropdown
     babSelectors.forEach(select => {
          select.addEventListener('change', calculateScore);
     });

     // Hitung skor saat halaman dimuat pertama kali
     calculateScore();
     });
    </script>
@endpush
