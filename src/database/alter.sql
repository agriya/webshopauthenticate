ALTER TABLE `users` CHANGE `user_id` `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `users` DROP `bba_token`;

-- Date added: 29/04/2014

ALTER TABLE `throttle` CHANGE `last_attempt_at` `last_attempt_at` TIMESTAMP NOT NULL ;
ALTER TABLE `throttle` CHANGE `created_at` `suspended_at` TIMESTAMP NOT NULL ;
ALTER TABLE `throttle` CHANGE `updated_at` `banned_at` TIMESTAMP NOT NULL ;