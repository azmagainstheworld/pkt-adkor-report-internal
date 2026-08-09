@extends('layouts.app')

@section('content')
<main class="flex-1 overflow-y-auto p-8 relative bg-[#F8F9FA]">
    
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Edit Data Karyawan</h2>
            <a href="{{ route('karyawan.index') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">Kembali</a>
        </div>

        <x-card class="p-6 !rounded-2xl">
            <form action="{{ route('karyawan.update', $karyawan->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama', $karyawan->nama) }}" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">NPK <span class="text-red-500">*</span></label>
                    <input type="text" name="npk" value="{{ old('npk', $karyawan->npk) }}" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $karyawan->tempat_lahir) }}" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $karyawan->tanggal_lahir) }}" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">No. HP / Telepon</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $karyawan->no_hp) }}" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Golongan / Grade <span class="text-red-500">*</span></label>
                    <select name="gol_grade" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500">
                        @foreach(['I-A','I-B','I-C','I-D','II-A','II-B','II-C','II-D','III-A','III-B','III-C','III-D','IV-A','IV-B','IV-C','IV-D'] as $gol)
                            <option value="{{ $gol }}" {{ $karyawan->gol_grade == $gol ? 'selected' : '' }}>{{ $gol }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal MPP / PBP <span class="text-red-500">*</span></label>
                    <input type="date" name="mpp_pbp" value="{{ old('mpp_pbp', $karyawan->mpp_pbp) }}" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan <span class="text-red-500">*</span></label>
                    <select name="keterangan" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500">
                        <option value="Organik" {{ $karyawan->keterangan == 'Organik' ? 'selected' : '' }}>Organik</option>
                        <option value="Non Organik" {{ $karyawan->keterangan == 'Non Organik' ? 'selected' : '' }}>Non Organik</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Lengkap</label>
                    <textarea name="alamat" rows="2" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 resize-none">{{ old('alamat', $karyawan->alamat) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Ukuran Kaos <span class="text-red-500">*</span></label>
                    <select name="ukuran_kaos" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500">
                        @foreach(['XS','S','M','L','XL','XXL','XXXL'] as $uk)
                            <option value="{{ $uk }}" {{ $karyawan->ukuran_kaos == $uk ? 'selected' : '' }}>{{ $uk }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipe Lengan Kaos <span class="text-red-500">*</span></label>
                    <select name="ukuran_kaos_tipe" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-blue-500">
                        <option value="3/4" {{ $karyawan->ukuran_kaos_tipe == '3/4' ? 'selected' : '' }}>Lengan 3/4</option>
                        <option value="Lengan Panjang" {{ $karyawan->ukuran_kaos_tipe == 'Lengan Panjang' ? 'selected' : '' }}>Lengan Panjang</option>
                    </select>
                </div>

                <div class="md:col-span-2 flex justify-end gap-3 mt-4">
                    <a href="{{ route('karyawan.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200">Batal</a>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 shadow-lg shadow-blue-600/20">Perbarui Data</button>
                </div>
            </form>
        </x-card>
    </div>

</main>
@endsection