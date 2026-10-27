<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Job extends Model {
    use SoftDeletes;
    protected $fillable = ['company_id', 'major_id', 'title', 'description', 'requirements', 'is_approved'];
    protected function casts(): array { return ['is_approved' => 'boolean']; }
    public function company() { return $this->belongsTo(Company::class); }
    public function major() { return $this->belongsTo(Major::class); }
    public function applications() { return $this->hasMany(Application::class); }

    /**
     * Mô tả công việc ở dạng văn bản thuần, dùng cho thẻ tóm tắt và prompt gửi AI.
     *
     * Mô tả được soạn bằng CKEditor nên là HTML. Nếu chỉ gọi strip_tags() thì các
     * mục danh sách sẽ dính liền nhau ("...kiểm thửThực hiện..."), vì vậy phải
     * chèn khoảng trắng tại vị trí các thẻ đóng khối trước khi gỡ thẻ.
     */
    public function getPlainDescriptionAttribute(): string
    {
        return static::toPlainText($this->description);
    }

    public function getPlainRequirementsAttribute(): string
    {
        return static::toPlainText($this->requirements);
    }

    public static function toPlainText(?string $html): string
    {
        $spaced = preg_replace('#</(li|p|h[1-6]|ul|ol|blockquote|div)>#i', ' ', (string) $html);

        return trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($spaced))));
    }
}