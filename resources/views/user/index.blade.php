@extends('layouts.main', ['title' => 'User'])
@section('title-content')
<i class="fas fa-user-tie mr-2"></i>
User
@endsection

@section('content')
@if (session('store') == 'success')
<x-alert type="success">
    <strong>Berhasil dibuat!</strong> User berhasil dibuat.
</x-alert>
@endif

@if (session('update') == 'success')
<x-alert type="success">
    <strong>Berhasil diupdate!</strong> User berhasil diupdate.
</x-alert>
@endif

@if (session('destroy') == 'success')
<x-alert type="success">
    <strong>Berhasil dihapus!</strong> User berhasil dihapus.
</x-alert>
@endif

@if (session('error'))
<x-alert type="danger">
    <strong>Gagal!</strong> {{ session('error') }}
</x-alert>
@endif

<div class="card card-orange card-outline">
    <div class="card-header form-inline">
        <a href="{{ route('user.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i> Tambah
        </a>
        {{-- 🔎 Form Search --}}
        <form action="{{ route('user.index') }}" method="get" class="ml-auto">
            <div class="input-group">
                <input type="text" class="form-control" name="search"
                       value="{{ request()->search }}"
                       placeholder="Cari nama atau username">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>id</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Role/Peran</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $key => $user)
                <tr>
                    <td>{{ $users->firstItem() + $key }}</td>
                    <td>{{ $user->nama }}</td>
                    <td>{{ $user->username }}</td>
                    <td>
                        @if($user->role == 'admin')
                        <span class="badge badge-danger">Admin</span>
                        @else
                        <span class="badge badge-primary">Petugas</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('user.edit', [
                            'user' => $user->id,
                            ]) }}" class="btn btn-xs text-success p-0 mr-1">
                            <i class="fas fa-edit"></i>
                        </a>
                        
                        <button type="button" data-toggle="modal" data-target="#modalDelete" data-url="{{ route('user.destroy', [
                            'user' => $user->id,
                            ]) }}" class="btn btn-xs text-danger p-0 btn-delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $users->links('vendor.pagination.bootstrap-4') }}
    </div>
</div>
@endsection

@push('modals')
<x-modal-delete />
@endpush