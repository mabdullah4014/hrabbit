<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsernameDriversTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('drivers', function (Blueprint $table) {
			$table->string('username');
		});
		$drivers = \App\Driver::all();
		foreach ($drivers as $key => $driver) {
			$driver->username = strtolower($driver->name) . strtolower($driver->last_name) . '_' . $driver->id;
			$driver->save();
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('drivers', function (Blueprint $table) {
			$table->dropColumn('username');
		});
	}
}
