@extends('layouts.app')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-dark">Ngành học đang đào tạo</h2>
    </div>

    <div class="glass-card p-4">
        <p class="text-muted mb-4">Hãy chọn các chuyên ngành mà trường của bạn đang đào tạo. Điều này sẽ giúp Doanh nghiệp dễ dàng tìm thấy trường của bạn hơn khi họ có nhu cầu tuyển dụng sinh viên từ các ngành này.</p>
        
        <form action="{{ route('university.majors.store') }}" method="POST">
            @csrf
            <div class="row">
                @foreach($allMajors as $major)
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="form-check custom-checkbox">
                            <input class="form-check-input" type="checkbox" name="majors[]" value="{{ $major->id }}" id="major_{{ $major->id }}" {{ in_array($major->id, $universityMajors) ? 'checked' : '' }}>
                            <label class="form-check-label text-dark" for="major_{{ $major->id }}">
                                {{ $major->name }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-4 pt-3 border-top border-secondary text-end">
                <button type="submit" class="btn btn-primary px-4">Lưu cập nhật</button>
            </div>
        </form>
    </div>
</div>

<style>
.custom-checkbox .form-check-input {
    width: 1.5em;
    height: 1.5em;
    margin-top: 0.1em;
    border-color: #6c757d;
}
.custom-checkbox .form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}
.custom-checkbox .form-check-label {
    padding-top: 0.25em;
    padding-left: 0.5em;
    font-size: 1.05rem;
}
</style>
@endsection