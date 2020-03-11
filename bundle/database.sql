-- dockerでユーザー作るときの注意
-- https://teratail.com/questions/106894

CREATE DATABASE gamelog_db DEFAULT CHARACTER SET utf8;
GRANT ALL PRIVILEGES ON gamelog_db.* TO gamelog_user@'%' IDENTIFIED BY 'gamelog_pass' WITH GRANT OPTION;

USE gamelog_db;

-- ゲームテーブル
-- ゲームのidとタイトル
CREATE TABLE games (
  game_id int not null,
  game_title varchar(255) not null,
  primary key (game_id)
);
ALTER TABLE games ADD rate_score decimal DEFAULT 0.0;

-- https://qiita.com/nanaco/items/78680e241a2202bb00ab
-- ↑一旦これでローカルのcsvをdockerのmysqlにコピー
--  コピー先はvar/lib/mysql-files/　だとその後のLOAD DATAがうまくいきやすい
-- https://stackoverflow.com/questions/32737478/how-should-i-tackle-secure-file-priv-in-mysql

'/Users/hideri/workspace/gamelog/bundle/game.csv'
-- LOAD DATA INFILE 'root/for/game.csv' INTO TABLE games FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '"';
LOAD DATA INFILE 'var/lib/mysql-files/game.csv' INTO TABLE games FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '"';


-- ユーザーテーブル
-- 退会しても情報を念の為残しておきたいので
-- 論理削除
CREATE TABLE users (
  user_id int not null auto_increment,
  user_name varchar(255) not null,
  email varchar(255) not null,
  password varchar(255) not null,
  image varchar(255) not null DEFAULT default.png,
  year varchar(4) not null,
  month varchar(2) not null,
  day varchar(2) not null,
  regist_date datetime not null,
  update_date datetime,
  delete_date datetime,
  delete_flg tinyint(1) unsigned not null default 0,
  primary key (user_id)
);


-- 投稿テーブル
-- 投稿されたレビューなど
CREATE TABLE posts (
  post_id int not null auto_increment,
  title varchar(255) not null,
  body text not null,
  liked_count int DEFAULT 0,
  user_id int not null,
  target_game_id int,
  created_date datetime not null,
  update_date datetime,
  primary key (post_id)
);

-- コメントテーブル
-- 記事に対するコメント
-- 誰がどの投稿にコメントしたのか
CREATE TABLE comments (
  comment_id int not null auto_increment,
  body text not null,
  user_id int not null,
  target_posts_id int not null,
  created_date datetime not null,
  update_date datetime,
  primary key (comment_id)
);

-- ブックマークテーブル
-- 誰が(user_id)、どのゲームを(game_id)
-- ブックマークしたのか判定する
CREATE TABLE bookmark (
  bookmark_id int unsigned not null auto_increment,
  user_id int unsigned not null,
  game_id int unsigned not null,
  primary key (bookmark_id)
);

-- レーティングテーブル
-- 誰が(user_id)、どのゲームを(game_id)
-- 評価したか
CREATE TABLE rates (
  rate_id int unsigned not null auto_increment,
  score int unsigned not null,
  user_id int unsigned not null,
  game_id int unsigned not null,
  primary key (rate_id)
);


-- いいねテーブル
-- 誰が(user_id)、どの投稿に(post_id)
-- いいねしたのか判定する
CREATE TABLE liked (
  liked_id int unsigned not null auto_increment,
  user_id int unsigned not null,
  post_id int unsigned not null,
  is_liked tinyint(1) unsigned not null default 0,
  primary key (liked_id)
);

