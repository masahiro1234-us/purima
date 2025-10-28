<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_path',
        'postal_code',
        'address',
        'building',
        'profile_completed',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        // 'password' => 'hashed',
    ];

    public function favorites()
    {
        return $this->belongsToMany(\App\Models\Item::class, 'favorites')->withTimestamps();
    }

    /**
     * プロフィール画像の表示用URL（外部URL/ローカル両対応）
     * - http(s) はそのまま
     * - 'storage/...' で始まる相対パスは先頭に '/' を付けて返す
     * - それ以外（ファイル名だけ等）は /storage/avatars/{filename} に正規化
     * - 未設定なら null を返す（Blade側でプレースホルダにフォールバック）
     */
    public function getAvatarUrlAttribute(): ?string
    {
        $raw = (string) ($this->avatar_path ?? '');

        if ($raw === '') {
            return null;
        }

        if (Str::startsWith($raw, ['http://', 'https://'])) {
            return $raw;
        }

        if (Str::startsWith($raw, ['storage/'])) {
            return '/' . ltrim($raw, '/');
        }

        $filename = basename($raw);
        if ($filename && $filename !== '.' && $filename !== '..') {
            return '/storage/avatars/' . $filename;
        }

        return null;
    }
}