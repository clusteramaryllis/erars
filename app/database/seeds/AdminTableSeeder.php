<?php

class AdminTableSeeder extends DatabaseSeeder {

	public function run()
	{
		Admin::create(array(
			"username" => "admin",
			"email" => "admin@admin.com",
			"password" => Hash::make("admin")
		));
	}

}