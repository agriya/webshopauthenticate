INSERT INTO `users` (`id`, `email`, `password`, `permissions`, `activated`, `activation_code`, `activated_at`, `last_login`, `persist_code`, `reset_password_code`, `first_name`, `last_name`, `created_at`, `updated_at`) VALUES(1, 'r.senthilvasan@agriya.in', '$2y$10$ELl8OgjHdWEBuf5xC5QBnOQfwkK.3nacWnixQl1PALZLFKcRgF2h.', '', 1, '', '2014-04-26 06:01:07', '2014-04-26 06:01:33', '$2y$10$jlg5bNAwJY4soaw8wTjZbeyr3umOEkqJXwow7EvRwrlj4AtEjKSq6', '', 'Senthil', 'Vasan', '2014-04-26 06:01:07', '2014-04-26 06:01:33');

INSERT INTO `groups` (`id`, `name`, `permissions`, `created_at`, `updated_at`) VALUES(1, 'Admin', '{"system":1}', '2014-04-26 06:01:07', '2014-04-26 06:01:07');

INSERT INTO `users_groups` (`user_id`, `group_id`) VALUES(1, 1);

INSERT INTO `throttle` (`id`, `user_id`, `ip_address`, `attempts`, `suspended`, `banned`, `last_attempt_at`, `created_at`, `updated_at`) VALUES(1, 1, '127.0.0.1', 0, 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00');