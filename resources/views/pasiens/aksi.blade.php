<div class="btn-group">
    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
        <i class="fas fa-cogs"></i> Aksi
    </button>
    <ul class="dropdown-menu">
        <li>
            <a href="{{ route('pasiens.show', $row->id) }}" class="dropdown-item">
                <i class="fas fa-eye me-2"></i> Detail Data Sasaran
            </a>
        </li>
        <li>
            <a href="{{ route('pasiens.edit', $row->id) }}" class="dropdown-item">
                <i class="fas fa-edit me-2"></i> Edit Data Sasaran
            </a>
        </li>
        <li>
            <button class="dropdown-item text-danger delete-btn" data-id="{{ $row->id }}" data-nama="{{ $row->name }}">
                <i class="fas fa-trash me-2"></i> Hapus Data Sasaran
            </button>
        </li>
    </ul>
</div>

<form id="delete-form-{{ $row->id }}" action="{{ route('pasiens.destroy', $row->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
