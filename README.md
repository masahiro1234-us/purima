Stripe秘密鍵は削除済み（プレースホルダに置き換え済み）

# Prima（フリマアプリ）

Laravel製のフリマアプリです。
ユーザー登録・ログイン・商品出品・購入・お気に入り・コメント投稿など、基本的な機能を備えています。
Stripe決済（テストモード）にも対応しています。
※ Stripe秘密鍵は削除済み（プレースホルダに置き換え済み）

---

## 🔧 環境構築

### 1. クローン & 起動
```bash
git clone git@github.com:masahiro1234-us/purima.git
cd purima
docker compose up -d --build
```

2.コンテナに入る
```bash
docker compose exec php bash
```

3.Laravel初期設定
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link


4.Stripeテストキー設定
.envに以下を追加
STRIPE_PUBLIC_KEY=YOUR_PUBLIC_KEY
STRIPE_SECRET_KEY=YOUR_SECRET_KEY

# 機能一覧
・ユーザー登録/ログイン＝Laravel標準の認証機能を利用
・プロフィール編集＝usersテーブルに直接格納（住所・建物名など）
・商品一覧/詳細＝画像１枚付き、価格・説明表示
・商品出品＝ログインユーザーが商品を登録（画像アップロード含む）
・商品購入＝Stripe Checkoutで決済、DBはbuyer_idを更新
・お気に入り登録＝ボタンでトグル切り替え
・コメント投稿＝ログインユーザーが商品にコメント可能
・マイページ＝購入・出品済みの商品の一覧表示
・テスト＝Featureテストで主要機能の自動確認

# テスト実行
.env.testing設定
APP_ENV=testing
DB_CONNECTION=mysql
DB_HOST=mysql
DB_DATABASE=demo_test
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

```bash
php artisan config:clear
php artisan migrate:fresh --env=testing
php artisan test --ent=testing


全てのテスト(10tests)がPASSすること確認ずみ

# 画像をアップロード
・public/storage/items/に保存されます。
・実際はURLはstorage/items/ファイル名。
・php artisan storage:link実行が必要。

# Stripe決済
・テストカード： 4242 4242 4242 4242 / 12/34 / 123
・決済後に items.buyer_id が更新されます。
・注文・支払い情報は別テーブルには保存しません。

# 使用技術
・フレームワーク＝Laravel 10.x
・コンテナ＝Docker(nginx, php, mysql, phpmyadmin)
・DB＝MySQL 8.x
・決済＝Stripe API(テストモード)
・テスト＝PHPUnit/Laravel Feature Test
・画像処理＝PHP-GD
・フロント＝Bladde +CSS（BEM準拠）