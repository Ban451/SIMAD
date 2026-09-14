@extends('layouts.app')

@section('title', 'Manajemen Dokumen')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Manajemen Dokumen</h4>
    @if(!auth()->user()->isSiswa())
        <a href="{{ route('dokumen.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Upload Dokumen
        </a>
    @endif
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Kategori</label>
                <select name="kategori" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($kategori as $k)
                        <option value="{{ $k->id }}" {{ request('kategori') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>
            @if(auth()->user()->isAdmin())
            <div class="col-md-3">
                <label class="form-label">Siswa</label>
                <select name="siswa" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Siswa</option>
                    @foreach($siswa as $s)
                        <option value="{{ $s->id }}" {{ request('siswa') == $s->id ? 'selected' : '' }}>
                            {{ $s->name }} ({{ $s->nis }})
                        </option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('dokumen.index') }}" class="btn btn-secondary ms-2">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Judul</th>
                        <th>Siswa</th>
                        <th>Kategori</th>
                        <th>File</th>
                        <th>Upload</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dokumen as $d)
                    <tr>
                        <td>{{ $loop->iteration + ($dokumen->currentPage() - 1) * $dokumen->perPage() }}</td>
                        <td>{{ $d->judul }}</td>
                        <td>{{ $d->siswa->name }}</td>
                        <td><span class="badge bg-info">{{ $d->kategori->nama_kategori }}</span></td>
                        <td>
                            <span class="badge bg-secondary">{{ $d->file_name }}</span>
                            <br><small class="text-muted">{{ number_format($d->file_size / 1024, 2) }} KB</small>
                        </td>
                        <td>
                            {{ $d->uploader->name }}<br>
                            <small class="text-muted">{{ $d->created_at->format('d/m/Y H:i') }}</small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('dokumen.download', $d->id) }}" class="btn btn-success" title="Download">
                                    <i class="bi bi-download"></i>
                                </a>
                                @if(!auth()->user()->isSiswa())
                                    <a href="{{ route('dokumen.edit', $d->id) }}" class="btn btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger" title="Hapus"
                                        onclick="confirmDelete('{{ route('dokumen.destroy', $d->id) }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada dokumen</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $dokumen->links() }}
    </div>
</div>

<!-- Form Delete -->
<form id="delete-form" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<script>
function confirmDelete(url) {
    if(confirm('Yakin ingin menghapus dokumen ini?')) {
        const form = document.getElementById('delete-form');
        form.action = url;
        form.submit();
    }
}
</script>
@endsection