<?php

namespace App\Models;

use App\Models\User;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'price',
        'brand',
        'description',
        'img_url',
        'status',
        'user_id',
        'buyer_id',
    ];

    // blade から $item->image_src で使えるように
    protected $appends = ['image_src'];

    /* ---------- リレーション ---------- */

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function favorites(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function isFavoritedBy(?User $user): bool
    {
        return $user
            ? $this->favorites()->where('user_id', $user->id)->exists()
            : false;
    }

    public function favoritesCount(): int
    {
        return $this->favorites()->count();
    }

    /* ---------- スコープ ---------- */

    public function scopeAvailable($query)
    {
        return $query->whereNull('buyer_id');
    }

    public function scopeBoughtBy($query, int $userId)
    {
        return $query->where('buyer_id', $userId);
    }

    /* ---------- 派生プロパティ ---------- */

    public function getPriceFormattedAttribute(): string
    {
        return number_format((int) $this->price);
    }

    public function getStatusLabelAttribute(): string
    {
        return (string) ($this->status ?? '良好');
    }

    /**
     * 画像の“実際にブラウザで開けるURL”を返す。
     * ルール:
     * - http/https で始まる → そのまま返す（外部URL）
     * - /storage または storage で始まる → そのまま相対URLで返す（/を補正）
     * - /image または image で始まる → そのまま相対URLで返す（/を補正）
     * - それ以外:
     *    - basename を取り、public/storage/items/{basename} が実在すれば /storage/items/{basename}
     *    - どれにも該当せず実ファイルも無い場合は空文字（imgを出さない）
     */
    public function getImageSrcAttribute(): string
    {
        $raw = trim((string) ($this->img_url ?? ''));

        if ($raw === '') {
            return '';
        }

        // 外部URL
        if (Str::startsWith($raw, ['http://', 'https://'])) {
            return $raw;
        }

        // 先頭スラッシュなし/ありを吸収
        $normalized = '/' . ltrim($raw, '/');

        // public配下のアセット（例: image/xxx.jpg）
        if (Str::startsWith($normalized, ['/image/'])) {
            // public/image/... が実在するならそのまま返す
            $publicPath = public_path(ltrim($normalized, '/'));
            return is_file($publicPath) ? $normalized : '';
        }

        // storage配下（シンボリックリンク経由で配信）
        if (Str::startsWith($normalized, ['/storage/'])) {
            $publicPath = public_path(ltrim($normalized, '/')); // public/storage/...
            return is_file($publicPath) ? $normalized : '';
        }

        // それ以外の値（例: 'items/xxx.jpg' や 'watch.jpg' など）→ basename を storage/items に当て込む
        $filename = basename($raw);
        if ($filename !== '' && $filename !== '.' && $filename !== '..') {
            $candidate = 'storage/items/' . $filename;         // public/storage/items/{filename}
            if (is_file(public_path($candidate))) {
                return '/' . $candidate;
            }
        }

        // ここまで来たら表示できるものが無い
        return '';
    }
}