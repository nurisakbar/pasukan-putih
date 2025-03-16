@extends('layouts.app')

@section('content')
<div class="app-content-header">
     <div class="container-fluid">
          <div class="row">
               <div class="col-sm-6">
                    <h3 class="mb-0">Edit Pasien</h3>
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
                                        <h5 class="card-title">Edit Data Pasien</h5>
                                   </div>
                              </div>
                         </div>
                         <div class="card-body">
                              <form action="{{ route('pasiens.update', $patient->id) }}" method="POST">
                                   @csrf
                                   @method('PUT')
                                   <div class="mb-3">
                                        <div class="row">
                                             <div class="col-sm-3">
                                                  <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                                             </div>
                                             <div class="col-sm-9">
                                                  <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $patient->name) }}" required>
                                             </div>
                                             @error('name')
                                                 <div class="text-danger">{{ $message }}</div>
                                             @enderror 
                                        </div>
                                   </div>
                                   <div class="mb-3">
                                        <div class="row">
                                             <div class="col-sm-3">
                                                  <label for="nik" class="form-label">NIK <span class="text-danger">*</span></label>
                                             </div>
                                             <div class="col-sm-9">
                                                  <input type="text" class="form-control" id="nik" name="nik" value="{{ old('nik', $patient->nik) }}" required>
                                             </div>
                                             @error('nik')
                                                 <div class="text-danger">{{ $message }}</div>
                                             @enderror 
                                        </div>
                                   </div>
                                   {{-- <div class="mb-3">
                                        <div class="row">
                                             <div class="col-sm-3">
                                                  <label for="jenis_kelamin" class="form-label">Jenis KTP <span class="text-danger">*</span></label>
                                             </div>
                                             <div class="col-sm-9">
                                                  <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>  
                                                       <option value="DKI" {{ $patient->jenis_kelamin == 'DKI' ? 'selected' : '' }}>DKI</option>
                                                       <option value="Non DKI" {{ $patient->jenis_kelamin == 'Non DKI' ? 'selected' : '' }}>Non DKI</option>
                                                  </select>
                                             </div>
                                             @error('jenis_kelamin')
                                                 <div class="text-danger">{{ $message }}</div>
                                             @enderror 
                                        </div>  
                                   </div> --}}
                                   <div class="mb-3">
                                        <div class="row">
                                             <div class="col-sm-3">
                                                  <label for="tanggal_lahir" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                             </div>
                                             <div class="col-sm-9">
                                                  <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="{{ old('tanggal_lahir', $patient->tanggal_lahir) }}" required>
                                             </div>
                                             @error('tanggal_lahir')
                                                 <div class="text-danger">{{ $message }}</div>
                                             @enderror
                                        </div>
                                   </div>
                                   <div class="mb-3">
                                        <div class="row">
                                             <div class="col-sm-3">
                                                  <label for="jenis_kelamin" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                             </div>
                                             <div class="col-sm-9">
                                                  <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>  
                                                       <option value="Laki-laki" {{ $patient->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                                       <option value="Perempuan" {{ $patient->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                                  </select>
                                             </div>
                                             @error('jenis_kelamin')
                                                 <div class="text-danger">{{ $message }}</div>
                                             @enderror 
                                        </div>  
                                   </div>
                                   <div class="mb-3">
                                        <div class="row">
                                             <div class="col-sm-3">
                                                  <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                                             </div>
                                             <div class="col-sm-9">
                                                  <input type="text" class="form-control" id="alamat" name="alamat" value="{{ old('alamat', $patient->alamat) }}" required>
                                             </div>
                                             @error('alamat')
                                                 <div class="text-danger">{{ $message }}</div>
                                             @enderror 
                                        </div>
                                   </div>
                                   <div class="mb-3">
                                        <div class="row">
                                             <div class="col-sm-3">
                                             </div>
                                             <div class="col-sm-1">
                                                  <label for="rt" class="form-label">RT</label>
                                                  <input type="number" class="form-control" id="rt" name="rt" value="{{ old('rt', $patient->rt) }}" required>
                                                  @error('rt')
                                                      <div class="text-danger">{{ $message }}</div> 
                                                  @enderror 
                                             </div>
                                             <div class="col-sm-1">
                                                  <label for="rw" class="form-label">RW</label>
                                                  <input type="number" class="form-control" id="rw" name="rw" value="{{ old('rw', $patient->rw) }}" required>
                                                  @error('rw')
                                                      <div class="text-danger">{{ $message }}</div> 
                                                  @enderror 
                                             </div>
                                             <div class="col-sm-2">
                                                  <label for="kelurahan" class="form-label">Kelurahan</label>
                                                  <input type="text" class="form-control" id="kelurahan" name="village_id" value="{{ old('kelurahan', $patient->village_id) }}" required>
                                                  @error('kelurahan')
                                                      <div class="text-danger">{{ $message }}</div> 
                                                  @enderror 
                                             </div>
                                             <div class="col-sm-2">
                                                  <label for="kecamatan" class="form-label">Kecamatan</label>
                                                  <input type="text" class="form-control" id="kecamatan" name="district_id" value="{{ old('kecamatan', $patient->district_id) }}" required>
                                                  @error('kecamatan')
                                                      <div class="text-danger">{{ $message }}</div> 
                                                  @enderror 
                                             </div>
                                             <div class="col-sm-3">
                                                  <label for="kabupaten_kota" class="form-label">Kabupaten/Kota</label>
                                                  <input type="text" class="form-control" id="kabupaten_kota" name="regency_id" value="{{ old('kabupaten_kota', $patient->regency_id) }}" required>
                                                  @error('kabupaten_kota')
                                                      <div class="text-danger">{{ $message }}</div> 
                                                  @enderror 
                                             </div>
                                        </div>
                                   </div>

                                   <button type="submit" class="btn btn-primary">Update</button>
                                   <a href="{{ route('pasiens.index') }}" class="btn btn-secondary">Kembali</a>
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
        
    </script>
@endpush