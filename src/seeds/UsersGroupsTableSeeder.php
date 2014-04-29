<?php namespace Agriya\Webshopauthenticate;

use Illuminate\Database\Seeder;

class UsersGroupsTableSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$now = date('Y-m-d H:i:s');

		$basicdata['email'] = 'r.senthilvasan@agriya.in';
		$basicdata['password'] = '$2y$10$ELl8OgjHdWEBuf5xC5QBnOQfwkK.3nacWnixQl1PALZLFKcRgF2h.';
		$basicdata['activated'] = 1;
		$basicdata['activated_at'] = $now;
		$basicdata['first_name'] = 'Senthil';
		$basicdata['last_name'] = 'Vasan';
		$basicdata['created_at'] = $now;
		$basicdata['updated_at'] = $now;
		\DB::table('users')->insert($basicdata);

		\DB::table('groups')->truncate();

		$now = date('Y-m-d H:i:s');
		$data['name'] = 'Admin';
		$data['permissions'] = '{"system":1}';
		$data['created_at'] = $now;
		$data['updated_at'] = $now;
		\DB::table('groups')->insert($data);

		$usr_data['user_id'] = 1;
		$usr_data['group_id'] = 1;
		\DB::table('users_groups')->insert($usr_data);
	}
}