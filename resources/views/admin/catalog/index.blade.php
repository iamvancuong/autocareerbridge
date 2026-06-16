@extends('layouts.app')
@section('content')
<div class="container py-4">
    <h2 class="text-dark mb-4">Quản lý Danh mục</h2>
    <div class="row g-4">
        <!-- Fields -->
        <div class="col-md-5">
            <div class="glass-card p-4">
                <h5 class="text-dark mb-3">Lĩnh vực hoạt động</h5>
                <form action="{{ route('admin.catalog.fields.store') }}" method="POST" class="d-flex gap-2 mb-3">
                    @csrf<input type="text" name="name" class="form-control form-control-glass" placeholder="Thêm lĩnh vực mới..." required>
                    <button class="btn btn-gradient btn-sm px-3"><i class="fa-solid fa-plus"></i></button>
                </form>
                <ul class="list-group list-group-flush">
                    @foreach($fields as $field)
                    <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center border-secondary text-dark">
                        <span>{{ $field->name }} <span class="badge bg-secondary ms-2">{{ $field->majors_count }} ngành</span></span>
                        <form action="{{ route('admin.catalog.fields.destroy', $field->id) }}" method="POST">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <!-- Majors -->
        <div class="col-md-7">
            <div class="glass-card p-4">
                <h5 class="text-dark mb-3">Ngành học</h5>
                <form action="{{ route('admin.catalog.majors.store') }}" method="POST" class="row g-2 mb-3">
                    @csrf
                    <div class="col-5"><input type="text" name="name" class="form-control form-control-glass" placeholder="Tên ngành học..." required></div>
                    <div class="col-5"><select name="field_id" class="form-select form-control-glass" required>
                        @foreach($fields as $f)<option value="{{ $f->id }}">{{ $f->name }}</option>@endforeach
                    </select></div>
                    <div class="col-2"><button class="btn btn-gradient w-100"><i class="fa-solid fa-plus"></i></button></div>
                </form>
                <ul class="list-group list-group-flush">
                    @foreach($majors as $major)
                    <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center border-secondary text-dark">
                        <span>{{ $major->name }} <span class="badge bg-primary ms-2">{{ $major->field->name ?? '' }}</span></span>
                        <form action="{{ route('admin.catalog.majors.destroy', $major->id) }}" method="POST">@csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection