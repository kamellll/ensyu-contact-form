<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
class Contact extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_id',
        'first_name',
        'last_name',
        'gender',
        'email',
        'tel',
        'address',
        'building',
        'detail',
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function scopeCategorySearch($query, $category_id)
    {
        if (!empty($category_id)) {
            $query->where('contacts.category_id', $category_id);
        }
        return $query;
    }
    public function scopeGenderSearch($query, $gender)
    {
        if (!empty($gender)) {
            $query->where('contacts.gender', $gender);
        }
        return $query;
    }
    /** LIKE用の簡易エスケープ */
    protected static function escapeLike(string $v, string $e = '\\'): string
    {
        return str_replace([$e, '%', '_'], [$e . $e, $e . '%', $e . '_'], $v);
    }

    /**
     * キーワード検索（name/emailのみ。テーブル名を完全修飾）
     * $match: 'exact' | 'partial'
     */
    public function scopeKeywordSearch($query, $keyword, $match = 'partial')
    {
        $keyword = trim((string) $keyword);
        if ($keyword === '')
            return $query;
        // まず「完全一致」があるかどうかをチェック（現在の条件を維持したまま）
        $probe = (clone $query)->where(function (Builder $q) use ($keyword) {
            $q->where('contacts.last_name', '=', $keyword)
                ->Where('contacts.first_name', '=', $keyword)
                ->orWhere('contacts.email', '=', $keyword);
        });
        if ($probe->exists()) {
            if (!empty($keyword)) {
                return $query->where('last_name', $keyword)->orWhere('first_name', $keyword)->orWhere('email', $keyword);
            }
        }
        $escaped = self::escapeLike($keyword);
        return $query->where('last_name', 'like', '%' . $escaped . '%')->orWhere('first_name', 'like', '%' . $escaped . '%')->orWhere('email', 'like', '%' . $escaped . '%');
    }
    public function scopeDateSearch($query, $date)
    {
        if (!empty($date)) {
            $query->whereDate('contacts.created_at', $date);
        }
        return $query;
    }
}
