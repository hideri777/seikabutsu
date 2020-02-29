# Gamelogについて ver1.0
## 概要
Gamelogはゲームのレビューサイトです。
以下のリポジトリに最新のものがあります。  
[GitHub - hideri777/seikabutsu: PHP practice project](https://github.com/hideri777/seikabutsu)

#### ver1.0での機能
- 会員登録
- ログイン、ログアウト
- レビューの投稿、編集、削除
- レビューに対するコメント
- 投稿に対してのいいね機能
- マイページでの登録内容の確認、編集

いいねやコメントなどは画面全体を再読み込みするのではなくて、jQueryのajaxを利用して非同期での実装を行いました。
ピックアップや人気のゲームの一覧ページ上では100件あるデータを20件ずつ表示してページング処理を行いました。

#### ゲーム情報
ゲーム情報はSteamAPIのGetAppListを利用しました。  
[ISteamApps インターフェイス (Steamworks ドキュメント)](https://partner.steamgames.com/doc/webapi/ISteamApps)  
こちらからゲームのidとタイトルが取得できたので、それをオンラインの変換ツールを利用してcsv形式に変換し、LOAD DATA INFILEでDBに格納してあります。その際、絵文字がうまく入らなかったので絵文字の部分は消しました。

以下のurlからゲームのヘッダー画像を取得できます。APPIDにはDBに格納してあるゲームのidが入ります。  
https://steamcdn-a.akamaihd.net/steam/apps/APPID/header.jpg  
ゲームの説明文などは取得できなかったのでダミーデータが入っています。

## ディレクトリの構成
MacOSのXAMPP上で開発しました。

↓ルートディレクトリ
/Applications/XAMPP/xamppfiles/htdocs/seikabutsu/

cssとjsはほぼHTMLのテンプレートからの流用です。  
self.jsというjsのみ自前での処理を実装してあります。
いくつか見た目のカスタマイズをしたかったprofile.phpやlogin.php、regist.phpなどの箇所ではBootstrapを利用して実装しました。

#### アプリの配置  
seikabutsu/  
　├ app/ アプリの中身  
　│　├ config/ 設定などを行う  
　│　├ functions/ 主にajaxで利用、DB書き換えや値を返すだけ  
　│　├ logs/ ログ  
　│　├ models/ モデルクラス  
　│　├ templates/ ビュー  
　│　└ templates_c/ twigのキャッシュ  
　│　  
　├ public/ CSSやJS、画像など  
　│　└ css/  
　│　└ fonts/  
　│　└ img/  
　│　└ js/  
　│　└ index.phpなど  
　│  
　├ vendor/ composerでインストールした諸々  
　│  
　├ gitignore  
　├ composer.json  
　└ composer.lock  


## 今後やりたいこと  
- [ ] ゲームをストック（お気に入り）して後でそれらを閲覧できる
- [ ] 他人のユーザーのプロフィール閲覧ができる
- [ ] プロフィールの画像をアップロードして、それをアイコンに反映できる
- [ ] 他のユーザーをフォローできる
- [ ] 好きな話題でチャットルームを作ることができる
- [ ] VirtualBoxやDockerなどの仮想環境で動作確認
etc..


## 参考資料など
- ブクログ
https://booklog.jp/
本のレビューサイト。Gamelogの基本機能はこちらのサイトを参考に作りました。

- HTMLテンプレート
 [Finloans - Free Payday Loan Website Template Design 2020 - Colorlib](https://colorlib.com/wp/template/finloans/)
デザイン面はこちらのテンプレートを利用しました。


## 思ったことや課題点など ver1.0
最初はnoteやはてなブログのようなブログサービスにしようかと思っていましたが、途中でSteamからゲームの情報が取得できそうだったためゲームに特化したレビューサイトに変更しました。  
そういった経緯があったため、途中で見た目を大きく変えることになりその移行作業に時間を取られてしまいました。  
仕様を最初にきっちり決めておくことの大切さを痛感しました。  

機能的には基本的なものは出来上がったと思いますが、まだまだやりたかったことを実現できなかったです。特にレビューサイトであればこそユーザー間のコミュニケーションをより強化できるSNS的なフォロー関連の機能が入れられなかったのは大きな課題です。
