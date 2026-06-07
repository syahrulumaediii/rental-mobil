@extends('layouts.app')

@section('title', 'Kategori Kendaraan')
@section('page-title', 'Kategori Kendaraan')
@section('breadcrumb', 'Admin / Edit Kategori Kendaraan')
@section('sidebar-nav')
    @include('components.sidebar-admin')
@endsection



@section('content')
<div class="container mx-auto px-4 py-6 max-w-lg">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-xl font-bold text-gray-800 mb-6">Edit Kategori Kendaraan</h1>

        <form action="{{ route('admin.kategori-kendaraan.update', $kategoriKendaraan->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="nama" class="block text-gray-700 text-sm font-bold mb-2">Nama Kategori *</label>
                <input type="text" name="nama" id="nama" value="{{ old('nama', $kategoriKendaraan->nama) }}" 
                    class="shadow appearance-none border @error('nama') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                @error('nama')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label id="deskripsi" class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="4" 
                    class="shadow appearance-none border @error('deskripsi') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('deskripsi', $kategoriKendaraan->deskripsi) }}</textarea>
                @error('deskripsi')
                    <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('admin.kategori-kendaraan.index') }}" class="text-gray-600 hover:text-gray-800 font-medium text-sm">Kembali</a>
                <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-200">
                    Perbarui Kategori
                </button>
            </div>
        </form>
    </div>
</div>
@endsection