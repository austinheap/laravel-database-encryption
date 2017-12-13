CREATE TABLE `test_models` (
  `id` int(10) UNSIGNED NOT NULL,
  `should_be_encrypted` longtext COLLATE utf8mb4_unicode_ci,
  `shouldnt_be_encrypted` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `test_models`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `test_models`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;
