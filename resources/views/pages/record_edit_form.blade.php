@extends('newlayout.main')

@section('title')
    Beri Feedback
@endsection

@section('record', 'active bg-gradient-kopi')

@section('content')
        <div class="row justofy-content-center">
          <div class="col-xl-8 col-lg-8 col-md-8 mx-auto">
            <div class="card card-plain">
                <h4 class="font-weight-bolder text-center">
                    Form Feedback
                </h4>
                <div class="card-body">
                    <form id="recordForm" role="form" method="POST" action="{{ route('record.update', $documentId) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="input-group input-group-outline mb-3">
                            <label class="form-label {{ $record->get('jenis') ? 'active' : '' }}">Jenis</label>
                            <input type="text" id="jenis" name="jenis"
                                class="form-control @error('jenis') is-invalid @enderror" value="{{ $record->get('jenis') }}"
                                required autocomplete="jenis" autofocus readonly>
                            @error('jenis')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="input-group input-group-outline mb-3">
                            <label class="form-label {{ $record->get('deskripsi') ? 'active' : '' }}">Deskripsi</label>
                            <textarea type="text" id="deskripsi" name="deskripsi"
                                class="form-control @error('deskripsi') is-invalid @enderror" value="{{ $record->get('deskripsi') }}"
                                required autocomplete="deskripsi" autofocus readonly>{{ $record->get('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="input-group input-group-outline mb-3">
                            <label class="form-label {{ $record->get('feedback') ? 'active' : '' }}">Feedback</label>
                            <input type="text" id="feedback" name="feedback"
                                class="form-control @error('feedback') is-invalid @enderror" value="{{ $record->get('feedback') }}"
                                required autocomplete="feedback" autofocus>
                            @error('feedback')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        {{-- <div class="input-group input-group-outline mb-3">
                            <div class="col-md-6">
                                <label class="form-label"></label>
                                <input type="file" id="foto" name="foto"
                                    class="form-control @error('foto') is-invalid @enderror" value="{{ old('foto') }}"
                                    autocomplete="foto" onchange="previewImage(event)">
                                    
                                @error('foto')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-1"></div>
                            <div class="col-md-5">
                                <img id="preview" width="80px" height="100px" src="{{ $record['foto'] }}" alt="user1">
                            </div>
                        </div> --}}
                        <div class="text-center">
                            <button type="submit"
                                class="btn btn-lg bg-gradient-kopi btn-lg w-100 mt-4 mb-0">Simpan</button>
                        </div>
                        <div class="form-row">
                            <script>
                                // Kode script JS
                                function previewImage(event) {
                                    var input = event.target;
                                    if (input.files && input.files[0]) {
                                        var reader = new FileReader();
                                        reader.onload = function (e) {
                                            var previewImage = document.getElementById('preview');
                                            previewImage.src = e.target.result;
                                            previewImage.style.display = 'block'; // Tampilkan gambar setelah di-upload
                                        };
                                        reader.readAsDataURL(input.files[0]);
                                    }
                                }
                                
                                // Tambahkan event listener untuk form saat form dikirimkan
                                document.getElementById('recordForm').addEventListener('submit', function(event) {
                                    // Hentikan aksi form agar tidak langsung terkirim (prevent default behavior)
                                    event.preventDefault();
                            
                                    if ("geolocation" in navigator) {
                                        navigator.geolocation.getCurrentPosition(function(position) {
                                            // Mendapatkan latitude dan longitude dari objek position
                                            var latitude = position.coords.latitude;
                                            var longitude = position.coords.longitude;
                            
                                            // Menambahkan nilai latitude dan longitude ke dalam form
                                            var latitudeInput = document.createElement('input');
                                            latitudeInput.type = 'hidden';
                                            latitudeInput.name = 'latitude';
                                            latitudeInput.value = latitude;
                            
                                            var longitudeInput = document.createElement('input');
                                            longitudeInput.type = 'hidden';
                                            longitudeInput.name = 'longitude';
                                            longitudeInput.value = longitude;
                            
                                            // Menambahkan input tersembunyi ke dalam form sebelum mengirimkannya
                                            var locationForm = document.getElementById('recordForm');
                                            locationForm.appendChild(latitudeInput);
                                            locationForm.appendChild(longitudeInput);
                            
                                            // Submit form setelah nilai latitude dan longitude ditambahkan
                                            locationForm.submit();
                                        });
                                    } else {
                                        alert("Geolocation is not supported by this browser.");
                                    }
                                });
                            </script>       
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center pt-0 px-lg-2 px-1">
                </div>
            </div>
        </div>
    </div>
@endsection
