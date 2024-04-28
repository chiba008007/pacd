# 高分子分析研究懇談会サイトリニューアル2024

## バージョン情報

- Laravel ver.8

- php ver.7.3.15

- mysql ver.5.6

## ローカル環境構築

- git clone

- Laravel install

    `composer install`

- Laravel key生成

    `php artisan key:generate`

- `.env` ファイル修正

- データベース構築

    `php artisan migrate`

- テストデータ追加

    `php artisan db:seed`

- npm環境構築

    `npm install`

    `npm run dev` or `npm run prod`

- サーバー起動

    `php artisan serve`

- 動作確認

    - Admin URL : http://127.0.0.1:8000/NfYn3iqa6uDh

    - Admin User : admin / password

    - User : user1 / password
- 画像が表示されないとき
 - シンボリックリンクが設定されていない可能性
 `php artisan storage:link`

- smoothでエラーの時
 banner_settingテーブルにidが1のデータを登録し、smoothを3にする


# PDFファイルの日本語化

`composer install`
- IPAフォントのダウンロード

- URL https://moji.or.jp/ipafont/ipa00303/

![ipa_font_download](https://user-images.githubusercontent.com/23257859/109404218-3758d500-79a7-11eb-8a21-fe03643f5f8b.png)
上記ファイル取得
- zipファイルとしてダウンロードされるので、解凍後、その中にあるファイルを先ほど作成したstorage/fonts/ディレクトリの下にコピーしてください。


# database.phpの修正
- mysqlのstrictをfalseに変更する

![image](https://user-images.githubusercontent.com/23257859/109950486-e71fa100-7d1f-11eb-908f-bdbd1e4bc36f.png)



# DBのバックアップ
- 毎日8時実行
    - 実行ファイルの名前がおかしいのは申し訳ないです
    - 曜日ごとにとっているので、最大7ファイルができる予定

- 場所
```
[root@tk2-256-37853 backup]# pwd
/var/tmp/backup
```

- エビデンス
```
[root@tk2-256-37853 pacd_new]# crontab -l
0 8 * * * php /var/www/html/pacd_new/artisan command:name 1>> /dev/null 2>&1
```

```
[root@tk2-256-37853 backup]# ls
_mysql_5.dump  mysql_5.dump
```
```
[root@tk2-256-37853 backup]# ls -l
total 1024
-rw-r--r-- 1 root root 523274 Jun 11 15:22 _mysql_5.dump
-rw-r--r-- 1 root root 523274 Jun 11 15:38 mysql_5.dump
```


# getsslの再設定方法
- 有効期限が30日をすぎたら行った方がよさそう
root で `./getssl pacd.jp` を実行
たぶん証明書が再作成されるので、apacheの再起動を行い反映


## 再起動コマンド
```
[root@tk2-256-37853 ~]# service httpd restart
Redirecting to /bin/systemctl restart httpd.service
```
pacd.jpが表示されることを確認
