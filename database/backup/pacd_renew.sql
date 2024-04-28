-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2021-02-19 02:09:19
-- サーバのバージョン： 10.4.11-MariaDB
-- PHP のバージョン: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `pacd_renew`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `login_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0:利用不可,1:利用可',
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- テーブルのデータのダンプ `admins`
--

INSERT INTO `admins` (`id`, `login_id`, `email`, `email_verified_at`, `password`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@sample.com', NULL, '$2y$10$mtNknzxtODd7UK/gcHjPLOfX7Fxez6.BgJx9VC7meCBQDJ7QGjZ7W', 1, NULL, '2021-02-18 17:08:47', '2021-02-18 17:08:47'),
(2, 'admin0', 'admin0@sample.com', NULL, '$2y$10$RWMorPYUJ26UruXnXTNIb.HIEthRQnuSg8sJnReytA36fIZQT7yTG', 0, NULL, '2021-02-18 17:08:47', '2021-02-18 17:08:47');

-- --------------------------------------------------------

--
-- テーブルの構造 `events`
--

CREATE TABLE `events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `enabled` int(11) NOT NULL DEFAULT 1 COMMENT '0:申込受付済み 1:申込受付中',
  `category_type` int(11) NOT NULL DEFAULT 1 COMMENT '1:共通, 2:例会, 3:高分子分析討論会, 4:講習会',
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'イベントコード',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '主催',
  `event_type` int(11) NOT NULL DEFAULT 1 COMMENT 'イベント型',
  `coworker` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '協賛',
  `date_start` date NOT NULL COMMENT '開催日',
  `date_end` date NOT NULL COMMENT '閉会日',
  `place` varchar(1280) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '場所',
  `event_address` varchar(1280) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '住所',
  `map_status` int(11) NOT NULL DEFAULT 0 COMMENT '地図の表示',
  `other` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '備考',
  `join_enable` int(11) NOT NULL DEFAULT 1 COMMENT '懇談会参加',
  `status` int(11) NOT NULL DEFAULT 1 COMMENT 'ステータス',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `event_attendees`
--

CREATE TABLE `event_attendees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0:参加費未払,1:参加費支払済',
  `is_presenter` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0:参加者,1:講演者',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `event_joins`
--

CREATE TABLE `event_joins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` int(11) NOT NULL COMMENT 'eventsテーブルのid',
  `number` int(11) NOT NULL COMMENT '並び順',
  `join_status` int(11) NOT NULL DEFAULT 1 COMMENT '有効/無効',
  `join_name` varchar(512) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '項目名',
  `join_price` int(11) NOT NULL DEFAULT 0 COMMENT '参加金額',
  `join_fee` int(11) NOT NULL DEFAULT 0 COMMENT '懇談会金額',
  `status` int(11) NOT NULL DEFAULT 1 COMMENT 'ステータス',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `connection` text COLLATE utf8_unicode_ci NOT NULL,
  `queue` text COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `form_data_common`
--

CREATE TABLE `form_data_common` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `form_input_value_id` int(11) NOT NULL,
  `data` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `data_sub` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'テキストエリア付きチェックボックスのテキストデータ',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `form_data_event`
--

CREATE TABLE `form_data_event` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_attendee_id` int(11) NOT NULL,
  `form_input_value_id` int(11) NOT NULL,
  `data` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `data_sub` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'テキストエリア付きチェックボックスのテキストデータ',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `form_inputs`
--

CREATE TABLE `form_inputs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `form_type` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT '項目名',
  `type` int(11) NOT NULL DEFAULT 1 COMMENT '1:textbox, 2:selectbox, 3:checkbox',
  `validation_rules` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `validation_message` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_display_published` tinyint(1) NOT NULL DEFAULT 0 COMMENT '公開画面に表示するか',
  `is_display_user_list` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'ユーザー一覧に表示するか',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `form_input_values`
--

CREATE TABLE `form_input_values` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `form_input_id` int(11) NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_included_textarea` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'テキストボックスを含むチェックボックスか',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- テーブルのデータのダンプ `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2021_01_05_082153_create_admins_table', 1),
(5, '2021_01_19_165441_create_page_contents', 1),
(6, '2021_01_19_165441_create_page_sub_contents', 1),
(7, '2021_01_19_165441_create_pages', 1),
(8, '2021_01_20_142625_create_account_types_table', 1),
(9, '2021_01_27_111755_add_column_to_page_sub_contents_table', 1),
(10, '2021_01_27_112129_add_is_opened_to_pages_table', 1),
(11, '2021_02_02_170023_create_events', 1),
(12, '2021_02_04_122017_program', 1),
(13, '2021_02_04_124242_program_lists', 1),
(14, '2021_02_04_142638_recreate_users_table', 1),
(15, '2021_02_04_175250_create_form_inputs_table', 1),
(16, '2021_02_04_175314_create_form_input_values_table', 1),
(17, '2021_02_04_181927_create_form_data_common_table', 1),
(18, '2021_02_04_181927_create_form_data_event_table', 1),
(19, '2021_02_08_004623_event_joins', 1),
(20, '2021_02_09_173623_add_event_type_events', 1),
(21, '2021_02_10_154227_create_uploads', 1),
(22, '2021_02_17_221440_drop_column_events_webex_url', 1),
(23, '2021_02_17_222106_add_webex_url_to_programs_table', 1),
(24, '2021_02_18_111755_add_delete_column_to_form_inputs_table', 1),
(25, '2021_02_18_111758_add_delete_column_to_users_table', 1),
(26, '2021_02_18_111759_add_delete_column_to_form_input_values_table', 1),
(27, '2021_02_18_142745_rename_category_type_to_form_type_on_form_inputs_table', 1),
(28, '2021_02_18_160726_create_event_attendees_table', 1),
(29, '2021_02_18_165718_create_presentations_table', 1),
(30, '2021_02_19_111758_add_is_form_column_to_pages_table', 1);

-- --------------------------------------------------------

--
-- テーブルの構造 `pages`
--

CREATE TABLE `pages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `uri` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `route_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_form` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'フォームページか',
  `is_opened` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0:非公開, 1:公開',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- テーブルのデータのダンプ `pages`
--

INSERT INTO `pages` (`id`, `title`, `description`, `uri`, `route_name`, `is_form`, `is_opened`, `created_at`, `updated_at`) VALUES
(1, '高分子分析研究懇談会', '高分子の分析に特化した学会です。', '/', 'top', 0, 1, '2021-02-18 17:08:47', '2021-02-18 17:08:47'),
(2, '高分子分析研究懇談会について', NULL, '/concept', 'concept', 0, 1, '2021-02-18 17:08:47', '2021-02-18 17:08:47'),
(3, '開催行事一覧', NULL, '/schedule', 'schedule', 0, 1, '2021-02-18 17:08:47', '2021-02-18 17:08:47'),
(4, '例会＆講演会', NULL, '/reikai', 'reikai', 0, 1, '2021-02-18 17:08:47', '2021-02-18 17:08:47'),
(5, '過去の例会一覧', NULL, '/reikai/history', 'reikai.history', 0, 1, '2021-02-18 17:08:47', '2021-02-18 17:08:47'),
(6, '高分子分析討論会', NULL, '/touronkai', 'touronkai', 0, 1, '2021-02-18 17:08:47', '2021-02-18 17:08:47'),
(7, '過去の討論会一覧', NULL, '/touronkai/history', 'touronkai.history', 0, 1, '2021-02-18 17:08:47', '2021-02-18 17:08:47'),
(8, '高分子分析技術講習会', NULL, '/koshikai', 'kosyukai', 0, 1, '2021-02-18 17:08:47', '2021-02-18 17:08:47'),
(9, '過去の講習会一覧', NULL, '/kosyukai/history', 'kosyukai.history', 0, 1, '2021-02-18 17:08:47', '2021-02-18 17:08:47'),
(10, '高分子分析ハンドブック', NULL, '/handbook', 'handbook', 0, 1, '2021-02-18 17:08:47', '2021-02-18 17:08:47'),
(11, '入会案内', NULL, '/nyukai', 'nyukai', 0, 1, '2021-02-18 17:08:47', '2021-02-18 17:08:47'),
(12, '運営委員会・企画委員会', NULL, '/iinkai', 'iinkai', 0, 1, '2021-02-18 17:08:47', '2021-02-18 17:08:47'),
(13, 'お問い合わせ', NULL, '/contact', 'contact', 0, 1, '2021-02-18 17:08:47', '2021-02-18 17:08:47'),
(14, 'リンク集', NULL, '/link', 'link', 0, 1, '2021-02-18 17:08:47', '2021-02-18 17:08:47'),
(15, '求人情報', NULL, '/kyujin', 'kyujin', 0, 1, '2021-02-18 17:08:47', '2021-02-18 17:08:47'),
(16, '個人情報について', NULL, '/privacy', 'privacy', 0, 1, '2021-02-18 17:08:47', '2021-02-18 17:08:47'),
(17, '会員登録', NULL, '/register', 'register', 1, 1, '2021-02-18 17:08:47', '2021-02-18 17:08:47'),
(18, '例会＆講演会参加申込', NULL, '/event/{event_id}/attend/{form_prefix}', 'reikai_attend', 1, 1, '2021-02-18 17:08:47', '2021-02-18 17:08:47'),
(24, 'テストページ', NULL, '/NfYn3iqa6uDh/pages/test', 'admin.pages.test', 0, 0, '2021-02-18 17:08:47', '2021-02-18 17:08:47');

-- --------------------------------------------------------

--
-- テーブルの構造 `page_contents`
--

CREATE TABLE `page_contents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `page_id` int(11) NOT NULL,
  `title` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'セクションのタイトル',
  `content` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'セクションの内容',
  `content_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text' COMMENT 'セクションのタイプ（例：list, tbale）',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `page_sub_contents`
--

CREATE TABLE `page_sub_contents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `page_content_id` int(11) NOT NULL,
  `content1` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `content2` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `column_count` int(11) NOT NULL DEFAULT 2 COMMENT '列数',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `presentations`
--

CREATE TABLE `presentations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `number` int(11) NOT NULL COMMENT '発表番号',
  `event_attendee_id` int(11) NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `proceeding` text COLLATE utf8_unicode_ci NOT NULL COMMENT '予稿原稿',
  `flash` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'フラッシュ原稿',
  `poster` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'ポスター原稿',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `programs`
--

CREATE TABLE `programs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` int(11) NOT NULL COMMENT 'eventsテーブルのid',
  `type` int(11) NOT NULL COMMENT '1:A会場 2:B会場',
  `date` date NOT NULL COMMENT '日程',
  `webex_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'webexurl',
  `explain` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '説明文',
  `status` int(11) NOT NULL DEFAULT 1 COMMENT 'ステータス',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `program_lists`
--

CREATE TABLE `program_lists` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` int(11) NOT NULL COMMENT 'eventsテーブルのid',
  `program_id` int(11) NOT NULL COMMENT 'programsテーブルのid',
  `number` int(11) NOT NULL COMMENT '並び順',
  `enable` int(11) NOT NULL DEFAULT 1 COMMENT '有効/無効',
  `ampm` int(11) NOT NULL DEFAULT 1 COMMENT '1:午前 2:午後',
  `start_hour` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '開始時',
  `start_minute` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '開始分',
  `end_hour` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '終了時',
  `end_minute` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '終了分',
  `speak_id` int(11) DEFAULT NULL COMMENT '講演者テーブルのID',
  `note` text COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '表示内容',
  `status` int(11) NOT NULL DEFAULT 1 COMMENT 'ステータス',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `uploads`
--

CREATE TABLE `uploads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` int(11) NOT NULL COMMENT 'eventsテーブルのid',
  `filename` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'ファイル名',
  `type` int(11) NOT NULL DEFAULT 0 COMMENT '1:開催案内 2:報告  3:要旨 4:その他',
  `dispname` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '表示名',
  `ext` varchar(11) COLLATE utf8_unicode_ci NOT NULL COMMENT '拡張子',
  `status` int(11) NOT NULL DEFAULT 1 COMMENT 'ステータス',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `login_id` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT '会員ID',
  `sei` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `mei` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `sei_kana` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `mei_kana` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `remarks` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` int(11) NOT NULL DEFAULT 1 COMMENT '会員区分(1:無料,2:個人,3:法人,4:協賛)',
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- テーブルのデータのダンプ `users`
--

INSERT INTO `users` (`id`, `login_id`, `sei`, `mei`, `sei_kana`, `mei_kana`, `email`, `password`, `remarks`, `type`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'user1', 'テスト', 'ユーザー1', 'てすと', 'ゆーざー', 'user1@sample.com', '$2y$10$GxX5VUlcGtSEdcCRYSFGFurCycI7H9BkJJXdsm/kPNiPOP.Av380.', 'テストユーザー', 1, NULL, '2021-02-18 17:08:46', '2021-02-18 17:08:46', NULL),
(2, 'user2', 'テスト', 'ユーザー2', 'てすと', 'ゆーざー', 'user2@sample.com', '$2y$10$bW/OyMx53MvzYD5UMwTEo.tUjYgk3gwYZFzdILul4kDmboGMSpaqK', 'テストユーザー', 2, NULL, '2021-02-18 17:08:46', '2021-02-18 17:08:46', NULL),
(3, 'user3', 'テスト', 'ユーザー3', 'てすと', 'ゆーざー', 'user3@sample.com', '$2y$10$aY0nGmWRHQxJhYMp0vLGcusyRO47K3GQBhneHm1Btinr/zEpo/6MG', 'テストユーザー', 3, NULL, '2021-02-18 17:08:46', '2021-02-18 17:08:46', NULL),
(4, 'user4', 'テスト', 'ユーザー4', 'てすと', 'ゆーざー', 'user4@sample.com', '$2y$10$/NGBAUFtLwDm294lLJbyG.H.S4TLj0YAJ52JFkJx5bAe.8EdvO8hu', 'テストユーザー', 4, NULL, '2021-02-18 17:08:47', '2021-02-18 17:08:47', NULL);

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_login_id_unique` (`login_id`);

--
-- テーブルのインデックス `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `events_code_unique` (`code`);

--
-- テーブルのインデックス `event_attendees`
--
ALTER TABLE `event_attendees`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `event_joins`
--
ALTER TABLE `event_joins`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- テーブルのインデックス `form_data_common`
--
ALTER TABLE `form_data_common`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `form_data_event`
--
ALTER TABLE `form_data_event`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `form_inputs`
--
ALTER TABLE `form_inputs`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `form_input_values`
--
ALTER TABLE `form_input_values`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pages_uri_unique` (`uri`),
  ADD UNIQUE KEY `pages_route_name_unique` (`route_name`);

--
-- テーブルのインデックス `page_contents`
--
ALTER TABLE `page_contents`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `page_sub_contents`
--
ALTER TABLE `page_sub_contents`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- テーブルのインデックス `presentations`
--
ALTER TABLE `presentations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `presentations_number_unique` (`number`);

--
-- テーブルのインデックス `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `program_lists`
--
ALTER TABLE `program_lists`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `uploads`
--
ALTER TABLE `uploads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uploads_filename_unique` (`filename`);

--
-- テーブルのインデックス `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_login_id_unique` (`login_id`);

--
-- ダンプしたテーブルのAUTO_INCREMENT
--

--
-- テーブルのAUTO_INCREMENT `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- テーブルのAUTO_INCREMENT `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `event_attendees`
--
ALTER TABLE `event_attendees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `event_joins`
--
ALTER TABLE `event_joins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `form_data_common`
--
ALTER TABLE `form_data_common`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `form_data_event`
--
ALTER TABLE `form_data_event`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `form_inputs`
--
ALTER TABLE `form_inputs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `form_input_values`
--
ALTER TABLE `form_input_values`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- テーブルのAUTO_INCREMENT `pages`
--
ALTER TABLE `pages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- テーブルのAUTO_INCREMENT `page_contents`
--
ALTER TABLE `page_contents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `page_sub_contents`
--
ALTER TABLE `page_sub_contents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `presentations`
--
ALTER TABLE `presentations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `programs`
--
ALTER TABLE `programs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `program_lists`
--
ALTER TABLE `program_lists`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `uploads`
--
ALTER TABLE `uploads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- テーブルのAUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
