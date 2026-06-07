@extends('layouts.app')

@section('title', 'Kategori Kendaraan')
@section('page-title', 'Kategori Kendaraan')
@section('breadcrumb', 'Admin / Kategori Kendaraan')
@section('sidebar-nav')
    @include('components.sidebar-admin')
@endsection


@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Daftar Kategori Kendaraan</h1>
        <a href="{{ route('admin.kategori-kendaraan.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200">
            + Tambah Kategori
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 shadow">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 shadow">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full leading-normal">
            <thead>
                <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Nama Kategori</th>
                    <th class="py-3 px-6 text-left">Deskripsi</th>
                    <th class="py-3 px-6 text-center">Jumlah Kendaraan</th>
                    <th class="py-3 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                @forelse($kategori as $item)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="py-3 px-6 text-left font-medium text-gray-900">{{ $item->nama }}</td>
                        <td class="py-3 px-6 text-left">{{ $item->deskripsi ?? '-' }}</td>
                        <td class="py-3 px-6 text-center">
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                {{ $item->kendaraan_count }}
                            </span>
                        </td>
                        <td class="py-3 px-6 text-center">
                            <div class="flex item-center justify-center space-x-2">
                                <a href="{{ route('admin.kategori-kendaraan.edit', $item->id) }}" class="text-yellow-600 hover:text-yellow-900 font-medium">Edit</a>
                                
                                <form action="{{ route('admin.kategori-kendaraan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-6 text-center text-gray-500">Belum ada data kategori.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $kategori->links() }}
    </div>
</div>
@endsection