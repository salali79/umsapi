
--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `email_verified_at`, `password`, `remember_token`, `status`, `role_id`, `faculty_id`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_by`, `deleted_at`) VALUES
(1, 'admin', 'admin', 'admin@domain.com', NULL, '$2y$10$yoQCEqfX99PQG7ywv9YLxuiSmDXhpVRJYyAdcFJ0AhawAlXfUMKNC', NULL, 1, 1, NULL, NULL, NULL, NULL, '2021-05-13 06:38:33', NULL, NULL),
(2, 'Developer', 'developer', 'developer@ums.com', NULL, '$2y$10$oDD9vMHAtIFFKmjqFA3g6.HSq92J6p.NDbOpjqKY4GTLDTLAPILKS', NULL, 1, 1, NULL, NULL, NULL, '2021-01-21 00:23:15', '2021-01-21 00:24:38', NULL, NULL),
(5, 'الاشراف المالي', 'user3', 'user3@domain.com', NULL, '$2y$10$UJrA1zZYFHhodimHnHxdl.qtyqI1bvi2YFpLe.3LOdyhXpr1oKobG', NULL, 1, 4, NULL, NULL, NULL, '2021-01-21 00:25:34', '2021-01-21 00:37:05', NULL, NULL),
(6, 'مدير المحفظة', 'user6', 'user6@admin.com', NULL, '$2y$10$5bK2vt0hzF1S5AY7tXYNZeA6qcUVPP2zamhCVAJjoJXDSjc/xRBVm', NULL, 1, 5, NULL, NULL, NULL, '2021-03-28 13:34:20', '2021-03-28 13:34:20', NULL, NULL),
(7, 'محاسب', 'user7', 'user7@gmail.com', NULL, '$2y$10$OhDSFZW0Y75Wj6rmnXbVN.ksopryOk6ahcYsvQ0uYt57XMfSAqXfi', NULL, 1, 6, NULL, NULL, NULL, '2021-04-12 10:35:40', '2021-05-26 05:56:58', NULL, NULL),
(8, 'دينا ابراهيم', 'user8', 'Williambtouma@gmail.com', NULL, '$2y$10$gjMkJl45IaXManLfWAI0tezvfYyZEUuEecofe73ofh2rUfQjrTPKC', NULL, 1, 5, NULL, NULL, NULL, '2021-05-11 14:10:30', '2021-05-11 14:10:30', NULL, NULL);
