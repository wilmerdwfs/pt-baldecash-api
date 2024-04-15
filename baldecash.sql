-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.1.38-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.3.0.6589
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para pt_baldecash
CREATE DATABASE IF NOT EXISTS `pt_baldecash` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;
USE `pt_baldecash`;

-- Volcando estructura para tabla pt_baldecash.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla pt_baldecash.failed_jobs: ~0 rows (aproximadamente)

-- Volcando estructura para tabla pt_baldecash.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla pt_baldecash.migrations: ~4 rows (aproximadamente)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(5, '2014_10_12_000000_create_users_table', 1),
	(6, '2014_10_12_100000_create_password_resets_table', 1),
	(7, '2019_08_19_000000_create_failed_jobs_table', 1),
	(8, '2019_12_14_000001_create_personal_access_tokens_table', 1);

-- Volcando estructura para tabla pt_baldecash.password_resets
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla pt_baldecash.password_resets: ~0 rows (aproximadamente)

-- Volcando estructura para tabla pt_baldecash.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=125 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla pt_baldecash.personal_access_tokens: ~111 rows (aproximadamente)
INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
	(1, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '01677b2aeea0f238269b616cbbf4fbafaf791e00e1187a84f69c238becac7f74', '["*"]', NULL, NULL, '2024-04-13 23:13:21', '2024-04-13 23:13:21'),
	(2, 'App\\Models\\User', 2, 'TOKEN_PT_BALDECASH', '8271f108a978a39552feeac90c3e9eafdfcd79753f2801933c49c33459bb5074', '["*"]', NULL, NULL, '2024-04-13 23:17:39', '2024-04-13 23:17:39'),
	(3, 'App\\Models\\User', 2, 'TOKEN_PT_BALDECASH', '05c344fec28c825948282145619fdece296b17ba139f4fcfcfca4f619fdd00e4', '["*"]', NULL, NULL, '2024-04-13 23:18:00', '2024-04-13 23:18:00'),
	(4, 'App\\Models\\User', 6, 'TOKEN_PT_BALDECASH', 'e1c8bbcaabb1eaa0a3ab095a285c5bdc008746b81f271be8f481fce93ebdb5bb', '["*"]', NULL, NULL, '2024-04-14 05:52:07', '2024-04-14 05:52:07'),
	(5, 'App\\Models\\User', 7, 'TOKEN_PT_BALDECASH', '6ae0c2f573de2c39138bb18806b35d3d2e924522481dda90009d7140de9362be', '["*"]', NULL, NULL, '2024-04-14 05:54:15', '2024-04-14 05:54:15'),
	(6, 'App\\Models\\User', 8, 'TOKEN_PT_BALDECASH', '7184c243bbc5b04f34f54af6e76565fb80570ec47e7d7266c5410cf6c4e4654e', '["*"]', NULL, NULL, '2024-04-14 06:02:05', '2024-04-14 06:02:05'),
	(7, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', 'b10ef202450976e557fc3e34cc7b36b7fe2769c6a8153faf6abd692802b49d09', '["*"]', NULL, NULL, '2024-04-14 21:27:07', '2024-04-14 21:27:07'),
	(8, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '8c76912f3664d3d8d410ffb8156fc1b9ede0293e17451ae0bb2fc37234524d9e', '["*"]', NULL, NULL, '2024-04-14 22:13:58', '2024-04-14 22:13:58'),
	(9, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '91671f3c605ee3e536ea79509532523a2f4a093def9a40d33438888b550944e8', '["*"]', NULL, NULL, '2024-04-14 22:14:19', '2024-04-14 22:14:19'),
	(10, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '14e36e4a217f162b14096e954aacfc6094212e9c56f7b0555216ba1483d5a4e2', '["*"]', NULL, NULL, '2024-04-14 22:14:24', '2024-04-14 22:14:24'),
	(11, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '0ccb35eac03fc21c60074cd167efd812656b4888d6bee9a16e42400b1a99a047', '["*"]', NULL, NULL, '2024-04-14 22:14:31', '2024-04-14 22:14:31'),
	(12, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', 'f7a7cdf66d0ea15fe39a7caaeefffb80f334409fe975304cf07d057978293c01', '["*"]', NULL, NULL, '2024-04-14 22:15:36', '2024-04-14 22:15:36'),
	(13, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', 'a3e16e4c4ba6236ea59e330dfe036fa63c872f77459f3e46c065ba611c7a87df', '["*"]', NULL, NULL, '2024-04-14 22:15:45', '2024-04-14 22:15:45'),
	(14, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '591229036c78e6fab25ea3f7dabcaa606cff2c1651342d128680a0c0c05d1640', '["*"]', NULL, NULL, '2024-04-14 22:15:59', '2024-04-14 22:15:59'),
	(15, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '24f3c1f65aa51f487b5ecb854e524733bb6c61cf81f8a0d94fe8c0ef24922539', '["*"]', NULL, NULL, '2024-04-14 22:16:08', '2024-04-14 22:16:08'),
	(16, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '13a5bb8dcde35e528271f462d90e484037025337e0c79ab79b46aab48304ecf3', '["*"]', NULL, NULL, '2024-04-14 22:16:38', '2024-04-14 22:16:38'),
	(17, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '55d2e78096814f09805476f24edeca5d45ba970a6a0ea9b1766ce0a0bdbaa92f', '["*"]', NULL, NULL, '2024-04-14 22:16:48', '2024-04-14 22:16:48'),
	(18, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '6038addff41730de568baa3b7b47828f0ad5fc150c8df19ecd55eda4b221bf93', '["*"]', NULL, NULL, '2024-04-14 22:18:00', '2024-04-14 22:18:00'),
	(19, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '3b551b348851c081e301a4d75c29f79d53f8f0d3c7e6ce633c457826927d37bc', '["*"]', NULL, NULL, '2024-04-14 22:18:05', '2024-04-14 22:18:05'),
	(20, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '39e9052f4ab321095891bb44f9bbc04c7044731a8ca87d8e3aafe7cd64602be2', '["*"]', NULL, NULL, '2024-04-14 22:18:16', '2024-04-14 22:18:16'),
	(21, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '52ad41a91aa2e1c66896d9bb3005438047b8ea0ac1c836e9df8d433884ae5f04', '["*"]', NULL, NULL, '2024-04-14 22:18:35', '2024-04-14 22:18:35'),
	(22, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '512c8122ce941eb1d1cc3ab62481927bf97f2c78558150822109a12d1862a6d3', '["*"]', NULL, NULL, '2024-04-14 22:18:41', '2024-04-14 22:18:41'),
	(23, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '7a5b2c3b1ae311d1e4b46cda435da3a8a6f1712eed97e6b4e7f0d0972819f649', '["*"]', NULL, NULL, '2024-04-14 22:18:53', '2024-04-14 22:18:53'),
	(24, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', 'ff4776ffc99a2bd62a866563d97289328b8ca071ef0f9b7c5e4b851b92a86e58', '["*"]', NULL, NULL, '2024-04-14 22:31:51', '2024-04-14 22:31:51'),
	(25, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '4801d28f40d7681b92b0c77737740a1cabd19438d624c60de1dbb0cfda6e2b90', '["*"]', NULL, NULL, '2024-04-14 22:31:55', '2024-04-14 22:31:55'),
	(26, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', 'b5f488908704805914b4a9bab2e7ad821f9d0910f08ecdac99152f4bf5ae7f02', '["*"]', NULL, NULL, '2024-04-14 22:32:22', '2024-04-14 22:32:22'),
	(27, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '972e86db7aeeb2d7052b8a0e83571f80c477ea9ecb35eb654f04d22e210916ce', '["*"]', NULL, NULL, '2024-04-14 22:32:24', '2024-04-14 22:32:24'),
	(28, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', 'b4bfe124cd463fc984c5381af17f42d64167f7e940879381a5b93f6fd2472d67', '["*"]', NULL, NULL, '2024-04-14 22:33:57', '2024-04-14 22:33:57'),
	(29, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '3487362c11b7da033353a00961164d78e77a39e91005926e0bd0ad4d1350bd3e', '["*"]', NULL, NULL, '2024-04-14 22:33:59', '2024-04-14 22:33:59'),
	(30, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '4a83e951d191921021aa28038246fec6be29675b71a01fb87cfd3d04fb7c9459', '["*"]', NULL, NULL, '2024-04-14 22:34:41', '2024-04-14 22:34:41'),
	(31, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '9bf18325112f55da30dc861ebf738d13466072d3c852427026fffa756401ed5f', '["*"]', NULL, NULL, '2024-04-14 22:34:48', '2024-04-14 22:34:48'),
	(32, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '63e552e886728ec6bdacac486ef335eecf83d115578fe5db7fd49a373d0f3443', '["*"]', NULL, NULL, '2024-04-14 22:34:51', '2024-04-14 22:34:51'),
	(33, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '53c26d2834f66b5991320612d01ed2c6494850a131b98aeea09d13679a9aa76b', '["*"]', NULL, NULL, '2024-04-14 22:34:54', '2024-04-14 22:34:54'),
	(34, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '4a40b045414bc381bac08f8c3665263ce971208def11be63e4b659a0985f5acd', '["*"]', NULL, NULL, '2024-04-14 22:36:04', '2024-04-14 22:36:04'),
	(35, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', 'cf44f42745b3c2e16ff297da6b31004273effd045be3ac3be2d300c55f582d59', '["*"]', NULL, NULL, '2024-04-14 22:38:44', '2024-04-14 22:38:44'),
	(36, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', 'ecd989a270e6023123891455e99b36b08be404e5b34a9f42b95815f2590028b4', '["*"]', NULL, NULL, '2024-04-14 22:59:54', '2024-04-14 22:59:54'),
	(37, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '64d9d199c113c672855466e560fc111db220f718b1f01bcd6997f11f36ab0ea6', '["*"]', NULL, NULL, '2024-04-14 23:00:18', '2024-04-14 23:00:18'),
	(38, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '4e01feec90608ad6bf7642662b26e4a97590ae79a1d2c0610545293edf6eb656', '["*"]', NULL, NULL, '2024-04-14 23:01:49', '2024-04-14 23:01:49'),
	(39, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '8b00df1e0dc863853ec60493cfa72570f76582736978172ea97f6386ae001477', '["*"]', NULL, NULL, '2024-04-14 23:03:25', '2024-04-14 23:03:25'),
	(40, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', 'bbe549e06174b5bbc1d055c4ea1c1e463300979d95c16abf29717f2ba29992ce', '["*"]', NULL, NULL, '2024-04-14 23:03:46', '2024-04-14 23:03:46'),
	(41, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '5a282a725e7dd714b5d37f0d853378cbf688076377f8cf02c2d190d8ae25e2bd', '["*"]', NULL, NULL, '2024-04-14 23:03:54', '2024-04-14 23:03:54'),
	(42, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '62b1956db078bf3d9c3004bf623b33c20ba216cc2d2f2736d218ac3061a549d1', '["*"]', NULL, NULL, '2024-04-14 23:04:17', '2024-04-14 23:04:17'),
	(43, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '1b3a689118ac7cf19be49eb9ee99f49b1cf8d7a601637dc0f20b8dd42891c58b', '["*"]', NULL, NULL, '2024-04-14 23:12:05', '2024-04-14 23:12:05'),
	(44, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '23efdf923f29f5d6889f3186e469f3c91308f765eda841ed25b2956ac554c29a', '["*"]', NULL, NULL, '2024-04-14 23:12:28', '2024-04-14 23:12:28'),
	(45, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '96f5198ba3019f98ff9edf1c2ad8a8cb0e9bf504a3f4635e9e20332632f5f49d', '["*"]', NULL, NULL, '2024-04-14 23:12:31', '2024-04-14 23:12:31'),
	(46, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '54811e4126d303ac5a5ea5779a02660e461831b85cfba5bb70b8808a4a70e7ce', '["*"]', NULL, NULL, '2024-04-14 23:12:32', '2024-04-14 23:12:32'),
	(47, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '9dc9586a93fc895ef33ee24c8e0bd1494f1af847b8f007438beacfbf4af3f6c2', '["*"]', NULL, NULL, '2024-04-14 23:12:59', '2024-04-14 23:12:59'),
	(48, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '4e9f79ba82b74dae5e61c396bf4129abbb9b9d6353fb380acba7f36c3e17e9bf', '["*"]', NULL, NULL, '2024-04-14 23:16:57', '2024-04-14 23:16:57'),
	(49, 'App\\Models\\User', 8, 'TOKEN_PT_BALDECASH', '3d379a8ba3b5bab39b819a494d01fc88e34ef8f48b63ae15a2ed2925e472d99d', '["*"]', NULL, NULL, '2024-04-14 23:17:28', '2024-04-14 23:17:28'),
	(50, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '08e7966cbc16ecb8b4f942d3b2176265b5061f024c960c83e63eb6f061d1a925', '["*"]', NULL, NULL, '2024-04-14 23:18:41', '2024-04-14 23:18:41'),
	(51, 'App\\Models\\User', 8, 'TOKEN_PT_BALDECASH', '7a910ce727bd9a6c5301acb3635fa25f5c6ded9f15c5abb61546d2eebc9b60d6', '["*"]', NULL, NULL, '2024-04-14 23:22:16', '2024-04-14 23:22:16'),
	(52, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '0d5841ce4652d32eb3ac23f8201002bb1a073b1ac230efeb9f2f57ab2236f7dd', '["*"]', NULL, NULL, '2024-04-14 23:23:32', '2024-04-14 23:23:32'),
	(53, 'App\\Models\\User', 8, 'TOKEN_PT_BALDECASH', 'cf58cf54528262782291cc6ff267558eea9f03075b670b214421fbdbc2c21aca', '["*"]', NULL, NULL, '2024-04-14 23:24:04', '2024-04-14 23:24:04'),
	(54, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '364dc4c78942a6abbd58e46bf57bc74bfde5e725f9607296e7adcde9ceac53e3', '["*"]', NULL, NULL, '2024-04-14 23:25:47', '2024-04-14 23:25:47'),
	(55, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '1026e194b56a572569446d6a270651e68867e285332a05c04e61402bddf80101', '["*"]', NULL, NULL, '2024-04-14 23:27:09', '2024-04-14 23:27:09'),
	(56, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', 'b7c6dc85dffaccd1af237a4aed9c44e908255d94d36361c451e885079242267a', '["*"]', NULL, NULL, '2024-04-14 23:30:07', '2024-04-14 23:30:07'),
	(57, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '952bd731cae9441d963dd95cb7dfa4cdbb5f6103249d65f864020e186e0ba672', '["*"]', NULL, NULL, '2024-04-14 23:32:03', '2024-04-14 23:32:03'),
	(58, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', 'b960fdb48320fa605096bd0c55dc2240ed830adaf14a9f49d8072d3a2bef89d7', '["*"]', NULL, NULL, '2024-04-14 23:33:27', '2024-04-14 23:33:27'),
	(59, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', 'efe8cc7981ae0785d536cca5ada0b9296dc0d27f9ce39740426d81e50458ac21', '["*"]', NULL, NULL, '2024-04-14 23:33:30', '2024-04-14 23:33:30'),
	(60, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '91f46866fe6d8946b26e68125e58b8dc1febc05e3abaf389961ccd4d4341344c', '["*"]', NULL, NULL, '2024-04-14 23:34:15', '2024-04-14 23:34:15'),
	(61, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '03c0614c56f3c52edcc4e25e112e06498090c83ddfaf0f680b6dfe67e3ac44a3', '["*"]', NULL, NULL, '2024-04-14 23:35:09', '2024-04-14 23:35:09'),
	(62, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', 'd2642287c3800b1d5207e93f455fb1f1d6e17775ba0d30ddbc54ba59bf050891', '["*"]', NULL, NULL, '2024-04-14 23:35:33', '2024-04-14 23:35:33'),
	(63, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '7296fda8c11d9306e10f183e319c4db0dc7b43216b8ab6a1a4a26201fbb942ed', '["*"]', NULL, NULL, '2024-04-14 23:35:46', '2024-04-14 23:35:46'),
	(64, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '0d658a9dcd3c10d4579b89898625b2aa3811e3e9ea83c3a892d6c839ce923567', '["*"]', NULL, NULL, '2024-04-14 23:36:07', '2024-04-14 23:36:07'),
	(65, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '86dd4e19fad8297b5477a48e0c9755f7a1da2e7de90b06b4f652576e1ff67221', '["*"]', NULL, NULL, '2024-04-14 23:36:37', '2024-04-14 23:36:37'),
	(66, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', 'fd4ac0b1f34577af6deacb069b527e93a5a3ee551763b47fa9a3a60fd91fc742', '["*"]', NULL, NULL, '2024-04-14 23:38:47', '2024-04-14 23:38:47'),
	(67, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '1c54c31d853b1b03f5a43b743ce637bc76c0013e802fb6e4c84990775c63f935', '["*"]', NULL, NULL, '2024-04-14 23:39:53', '2024-04-14 23:39:53'),
	(68, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '90e849b6168ad7f88e1a4169a74a7320501ca9736f463313f0554d5d40841b4b', '["*"]', NULL, NULL, '2024-04-14 23:40:25', '2024-04-14 23:40:25'),
	(69, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', 'a5f98604968cc5141e93271e39e523f4db85bbf43834336cb26ac1fc160820f6', '["*"]', NULL, NULL, '2024-04-14 23:41:04', '2024-04-14 23:41:04'),
	(70, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', 'a8d196805af44248019989eb4b8ac93f00b831530da4ec4374479c857c14e4b4', '["*"]', NULL, NULL, '2024-04-14 23:41:15', '2024-04-14 23:41:15'),
	(71, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', 'efc495664098ba6036affc7af1b43136d169559b26d848d72bb384dfa88ddc18', '["*"]', NULL, NULL, '2024-04-14 23:41:53', '2024-04-14 23:41:53'),
	(72, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', 'e5cf7ed01b7e4b3d3ba6c2caf7e12905dabb1bfafbf2435bdfc7f0f5317fc702', '["*"]', NULL, NULL, '2024-04-14 23:44:52', '2024-04-14 23:44:52'),
	(73, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '42dc5f2f21911600da55accebc44d1fbffaca53b52ddf4bfdf6074c4b6d243fc', '["*"]', NULL, NULL, '2024-04-14 23:45:40', '2024-04-14 23:45:40'),
	(74, 'App\\Models\\User', 8, 'TOKEN_PT_BALDECASH', 'cf8bfca6974af4647116958914f89bd0f075680dc149629d0bdc82272eb49ec7', '["*"]', NULL, NULL, '2024-04-14 23:46:38', '2024-04-14 23:46:38'),
	(75, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', 'b20ea95dd5fa5fda8a1be36f787b1adc52c5ca403a741dba9f97c14792514efc', '["*"]', NULL, NULL, '2024-04-15 04:03:28', '2024-04-15 04:03:28'),
	(76, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', 'b1e503d43555b6546fdf929c1ec00d540094ce563488e12db108ba282cbf785b', '["*"]', NULL, NULL, '2024-04-15 05:45:52', '2024-04-15 05:45:52'),
	(77, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', 'e39980930580eb0414470e3de724d652d5648236301f6bcddf3cf47ccd6c0af6', '["*"]', NULL, NULL, '2024-04-15 05:48:28', '2024-04-15 05:48:28'),
	(78, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '3e4b95544915be44e42e558bea598f97d89ea6a75641186b17e552f1f57bf43e', '["*"]', NULL, NULL, '2024-04-15 05:52:27', '2024-04-15 05:52:27'),
	(79, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', 'c1c6f9679cc090b884b2b56a4c8358cb13bb3a60842af960650f716d8bd84778', '["*"]', NULL, NULL, '2024-04-15 05:52:56', '2024-04-15 05:52:56'),
	(80, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '3531bbaf70603f339ef0bf3de8a0177679560c65dfeea9001879a6f23b52206a', '["*"]', NULL, NULL, '2024-04-15 05:53:14', '2024-04-15 05:53:14'),
	(81, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', 'cefdbe58499b9d2ee7bd413ddf23da4568430baae4bcb350eb79de41ba3644e5', '["*"]', NULL, NULL, '2024-04-15 06:04:39', '2024-04-15 06:04:39'),
	(82, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '0a69a3ce0a604798aba0c61214c132e6ffaccb18f6b1b689ae3b39749fc6b290', '["*"]', NULL, NULL, '2024-04-15 06:06:28', '2024-04-15 06:06:28'),
	(83, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '8a5567f097dd9c4e9caa44adae1dfc8ccc13da47be094548eb1ba975dee14818', '["*"]', NULL, NULL, '2024-04-15 06:39:44', '2024-04-15 06:39:44'),
	(84, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', 'eeefa2b06d136a414938171fe07c2f6c7b028f00cf1f3240c5fd28f411833599', '["*"]', NULL, NULL, '2024-04-15 06:40:10', '2024-04-15 06:40:10'),
	(85, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '5764c5ae16e4cd7d308ab63a87940b802e6f428c6113e2f6d82179ac8a71181b', '["*"]', NULL, NULL, '2024-04-15 07:15:29', '2024-04-15 07:15:29'),
	(86, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '558a313b8ed8251b8fb2797b4c64dda9ae74dbe5b335c06d2578da2fa0ad7c49', '["*"]', NULL, NULL, '2024-04-15 07:18:53', '2024-04-15 07:18:53'),
	(87, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '76d2f5c90ef01787af7479c709acfd698fc003704fb2d6bd499e64366d589b60', '["*"]', NULL, NULL, '2024-04-15 07:26:54', '2024-04-15 07:26:54'),
	(88, 'App\\Models\\User', 9, 'TOKEN_PT_BALDECASH', 'af1de96478cefd551d50ce622393e5f1214f0199235c13abc263138eee4d008f', '["*"]', NULL, NULL, '2024-04-15 07:46:49', '2024-04-15 07:46:49'),
	(89, 'App\\Models\\User', 10, 'TOKEN_PT_BALDECASH', '0243932a17c3680118e3b807830b6e1ad55a39240aa9be67d9c5fc6e6f8db8c8', '["*"]', NULL, NULL, '2024-04-15 07:49:47', '2024-04-15 07:49:47'),
	(90, 'App\\Models\\User', 11, 'TOKEN_PT_BALDECASH', '4a880c174a1ecbb5357e7302c995972c92942e921ee3c54386d7ed2b6abd3092', '["*"]', NULL, NULL, '2024-04-15 08:13:13', '2024-04-15 08:13:13'),
	(91, 'App\\Models\\User', 12, 'TOKEN_PT_BALDECASH', 'ba88baff69df9b32e8a401f38bdb79fae690f08112c30f4af39ac58d5692f1f8', '["*"]', NULL, NULL, '2024-04-15 08:15:24', '2024-04-15 08:15:24'),
	(92, 'App\\Models\\User', 13, 'TOKEN_PT_BALDECASH', '3b58512a6a7f04b3f4227fa58d1d03b72ae1892b10cd51773645a027ed170844', '["*"]', NULL, NULL, '2024-04-15 08:32:43', '2024-04-15 08:32:43'),
	(93, 'App\\Models\\User', 14, 'TOKEN_PT_BALDECASH', '9cc82deabba09ae0862b5bb881beb95fc1cabbe7661f000162f465df9a61e87c', '["*"]', NULL, NULL, '2024-04-15 08:33:06', '2024-04-15 08:33:06'),
	(94, 'App\\Models\\User', 15, 'TOKEN_PT_BALDECASH', 'c198c1a35b50cdb344e027a15e9ded11666f7c975bff4ae9a450a7d0b259767a', '["*"]', NULL, NULL, '2024-04-15 08:33:55', '2024-04-15 08:33:55'),
	(95, 'App\\Models\\User', 16, 'TOKEN_PT_BALDECASH', 'acd1d57851168a9fba75395d42ffce76059466df337a0ab5638c5b904cc686ad', '["*"]', NULL, NULL, '2024-04-15 08:35:09', '2024-04-15 08:35:09'),
	(96, 'App\\Models\\User', 17, 'TOKEN_PT_BALDECASH', '7bfa7c8f49494ee4b43db56a41c13198c6823e2b4ee4517b8fbf78ab9f1ee406', '["*"]', NULL, NULL, '2024-04-15 08:36:15', '2024-04-15 08:36:15'),
	(97, 'App\\Models\\User', 18, 'TOKEN_PT_BALDECASH', '053f4add8aa6545d7fa686df54a1003bf1df5739d0efa77da11f681e6b0a5ba8', '["*"]', NULL, NULL, '2024-04-15 08:48:44', '2024-04-15 08:48:44'),
	(98, 'App\\Models\\User', 19, 'TOKEN_PT_BALDECASH', 'c84e2182a858987c0801f4487a7b798fb4f4370b01d877b9b4ea6d67d7478ec6', '["*"]', NULL, NULL, '2024-04-15 08:49:49', '2024-04-15 08:49:49'),
	(99, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '4d82970b8510fa9b256e73c7f5bfd57484e3f277ef52b96a5c99e93c8a361d44', '["*"]', NULL, NULL, '2024-04-15 09:20:14', '2024-04-15 09:20:14'),
	(100, 'App\\Models\\User', 1, 'TOKEN_PT_BALDECASH', '1c84186f4fe5b72552131c90efa70236f3e37eca4fff0b5139e36617c5e3ea76', '["*"]', NULL, NULL, '2024-04-15 17:27:54', '2024-04-15 17:27:54'),
	(101, 'App\\Models\\User', 20, 'TOKEN_PT_BALDECASH', '802f1033fbc7483d34cd37cac49d896550162ed5ae7b282f6b014658d4a597d8', '["*"]', NULL, NULL, '2024-04-15 22:25:59', '2024-04-15 22:25:59'),
	(102, 'App\\Models\\User', 21, 'TOKEN_PT_BALDECASH', '5eeb2bd7ba3e956a2fb31306d38b3dd2cf78dc65a22a93ee5189885c7fd3a95a', '["*"]', NULL, NULL, '2024-04-15 23:15:25', '2024-04-15 23:15:25'),
	(103, 'App\\Models\\User', 9, 'TOKEN_PT_BALDECASH', '8c4ed353ed0096de2f8850b7be590eca6a22a110c39cd56bca3826cf28973a6d', '["*"]', '2024-04-15 23:30:31', NULL, '2024-04-15 23:30:17', '2024-04-15 23:30:31'),
	(104, 'App\\Models\\User', 9, 'TOKEN_PT_BALDECASH', '40231ce8ca775b5ac0fe63f8750ed73f65b65f9f7fc4e5dcbaf2402cf9b390ef', '["*"]', '2024-04-16 00:14:13', NULL, '2024-04-15 23:30:49', '2024-04-16 00:14:13'),
	(105, 'App\\Models\\User', 22, 'TOKEN_PT_BALDECASH', '8bb2d7262163845180a187a652d353de0050d0c604213f95aa6c30564b025ee5', '["*"]', NULL, NULL, '2024-04-15 23:34:13', '2024-04-15 23:34:13'),
	(106, 'App\\Models\\User', 23, 'TOKEN_PT_BALDECASH', 'f57f653a4d7135c78ab27355da07a31430f9bbc132fa3dd68703d84455085713', '["*"]', NULL, NULL, '2024-04-15 23:50:03', '2024-04-15 23:50:03'),
	(107, 'App\\Models\\User', 24, 'TOKEN_PT_BALDECASH', '8ccf42dbde3725c3cea7ee09e2d533d4dafc84ce022c01e71e85629177397f52', '["*"]', NULL, NULL, '2024-04-15 23:52:15', '2024-04-15 23:52:15'),
	(108, 'App\\Models\\User', 25, 'TOKEN_PT_BALDECASH', '929192fd8595228b67c356c70a719db9aa9597aa715d093d080faba9d5c657e0', '["*"]', NULL, NULL, '2024-04-15 23:53:33', '2024-04-15 23:53:33'),
	(109, 'App\\Models\\User', 26, 'TOKEN_PT_BALDECASH', '35d36017ccce644ac23bd0175ef730716c4054212898a3d180de737a62956631', '["*"]', NULL, NULL, '2024-04-15 23:55:30', '2024-04-15 23:55:30'),
	(110, 'App\\Models\\User', 27, 'TOKEN_PT_BALDECASH', '88be73750e4d34548d25e36b9ff0a72424d9fdce4a5bc89476159d5d20840aa9', '["*"]', NULL, NULL, '2024-04-15 23:55:51', '2024-04-15 23:55:51'),
	(111, 'App\\Models\\User', 28, 'TOKEN_PT_BALDECASH', '36477f0f190d41351ca6aa306efbbb563aabb94c5c15037047b17c4acbd06964', '["*"]', NULL, NULL, '2024-04-15 23:56:09', '2024-04-15 23:56:09'),
	(112, 'App\\Models\\User', 9, 'TOKEN_PT_BALDECASH', '31fe856774d47c5075e5138ddde0669f48ba6bc2ef502757299680daf2fa2871', '["*"]', '2024-04-16 01:07:48', NULL, '2024-04-16 01:07:27', '2024-04-16 01:07:48'),
	(113, 'App\\Models\\User', 9, 'TOKEN_PT_BALDECASH', 'db6ec7d13e17b4d6b4017c296a41fba48fc41497391ef3ff99753817c2fee5a0', '["*"]', '2024-04-16 01:08:09', NULL, '2024-04-16 01:08:08', '2024-04-16 01:08:09'),
	(114, 'App\\Models\\User', 9, 'TOKEN_PT_BALDECASH', '9d81c1f70ccc7934baa0ffaf7658121b2fbf41e9836bb98fe6a24041b79facd2', '["*"]', '2024-04-16 01:13:37', NULL, '2024-04-16 01:13:37', '2024-04-16 01:13:37'),
	(115, 'App\\Models\\User', 9, 'TOKEN_PT_BALDECASH', '639825852865cdf46bcac63cd6d10ceb781ed88ed20fdca8819bd8994badb949', '["*"]', '2024-04-16 01:14:28', NULL, '2024-04-16 01:14:07', '2024-04-16 01:14:28'),
	(116, 'App\\Models\\User', 9, 'TOKEN_PT_BALDECASH', 'c5929293d54c84400a13d561b29a1467f81f4d6595b2852ca65ffa00afd1ca20', '["*"]', '2024-04-16 01:15:00', NULL, '2024-04-16 01:14:59', '2024-04-16 01:15:00'),
	(117, 'App\\Models\\User', 9, 'TOKEN_PT_BALDECASH', 'e7abad17bbe0552a7af1bb9abb34bc6503660ab716d6748d7e704a7240f303b3', '["*"]', '2024-04-16 01:28:18', NULL, '2024-04-16 01:23:09', '2024-04-16 01:28:18'),
	(118, 'App\\Models\\User', 9, 'TOKEN_PT_BALDECASH', '5add12281968a82c6041c47311f849699a835c8eb21db010fe454dfa97a0f124', '["*"]', '2024-04-16 01:31:12', NULL, '2024-04-16 01:28:51', '2024-04-16 01:31:12'),
	(119, 'App\\Models\\User', 9, 'TOKEN_PT_BALDECASH', 'a6ff60cadd3101ceb00fa919a586c0c6bea97169afee0a766fb2ceabcb0045a5', '["*"]', '2024-04-16 01:33:34', NULL, '2024-04-16 01:33:29', '2024-04-16 01:33:34'),
	(120, 'App\\Models\\User', 9, 'TOKEN_PT_BALDECASH', '8f40d5cbd11a0e1ee60623497c18c5beba17307804349d04aafa11ca9ffcf1ad', '["*"]', '2024-04-16 01:34:33', NULL, '2024-04-16 01:34:26', '2024-04-16 01:34:33'),
	(121, 'App\\Models\\User', 9, 'TOKEN_PT_BALDECASH', '82000bc7df16b0bf64a8efc7b0f963c4b26a7f3cb4c418878820947202c69f03', '["*"]', '2024-04-16 01:36:51', NULL, '2024-04-16 01:36:48', '2024-04-16 01:36:51'),
	(122, 'App\\Models\\User', 9, 'TOKEN_PT_BALDECASH', '6a2be0c935528c1cfbe46fdfaccdf8533065bada8df9ac477894fae441680ed6', '["*"]', '2024-04-16 01:49:20', NULL, '2024-04-16 01:49:14', '2024-04-16 01:49:20'),
	(123, 'App\\Models\\User', 9, 'TOKEN_PT_BALDECASH', 'a06ad789d38810eb479fb453398b170f9efb92a463dd50595f735d7ef23c9959', '["*"]', '2024-04-16 01:50:20', NULL, '2024-04-16 01:50:15', '2024-04-16 01:50:20'),
	(124, 'App\\Models\\User', 9, 'TOKEN_PT_BALDECASH', 'd5f67136673d0737868b4acc490a6bbc40f246412a1acea3f0d0bcb64c56ec4e', '["*"]', '2024-04-16 01:53:37', NULL, '2024-04-16 01:50:40', '2024-04-16 01:53:37');

-- Volcando estructura para tabla pt_baldecash.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `nombres` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '''''',
  `apellidos` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '''''',
  `rol` int(1) NOT NULL DEFAULT '0',
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla pt_baldecash.users: ~1 rows (aproximadamente)
INSERT INTO `users` (`id`, `email`, `fecha`, `nombres`, `apellidos`, `rol`, `password`, `created_at`, `updated_at`) VALUES
	(9, 'canipademon@gmail.com', '2024-04-15 20:00:22', 'Javier ', 'Martinez Ferreira', 1, '$2y$10$r2pK6GtGDancmuPXy2HtR.gAOa19rrwgKPS3JSJLMH3s/S7zsy8lO', '2024-04-15 07:46:49', '2024-04-16 00:04:02');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
