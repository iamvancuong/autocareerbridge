{{--
    Nạp CKEditor 5 (bản Classic) cho mọi <textarea class="rich-editor"> trên trang.

    Thanh công cụ được giới hạn đúng bằng những thẻ mà bộ lọc HTML phía máy chủ
    cho phép (xem hồ sơ 'job_content' trong config/purifier.php), để nhà tuyển
    dụng không định dạng ra thứ rồi bị lọc mất khi lưu.
--}}
@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('textarea.rich-editor').forEach(function (textarea) {
        // Trình duyệt không thể focus vào ô bắt buộc đã bị CKEditor ẩn đi,
        // nên bỏ `required` ở client; phía máy chủ vẫn kiểm tra đầy đủ.
        textarea.removeAttribute('required');

        ClassicEditor.create(textarea, {
            // Bản Classic dựng sẵn không kèm plugin Underline nên không khai báo
            // nút này, tránh cảnh báo "toolbarview-item-unavailable".
            toolbar: [
                'heading', '|',
                'bold', 'italic', '|',
                'bulletedList', 'numberedList', 'blockQuote', '|',
                'link', '|',
                'undo', 'redo'
            ],
            language: 'vi',
        }).catch(function (error) {
            // Không nạp được editor thì để nguyên textarea thường, form vẫn dùng được.
            console.error('Không khởi tạo được CKEditor:', error);
        });
    });
});
</script>
<style>
    .ck-editor__editable_inline { min-height: 180px; }
</style>
@endpush
