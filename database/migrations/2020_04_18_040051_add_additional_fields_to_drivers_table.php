<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdditionalFieldsToDriversTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('drivers', function (Blueprint $table) {
			$table->string('insurance')->nullable();
			$table->string('reg_sticker')->nullable();
			$table->string('experience')->nullable();
			$table->string('referral_code')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('drivers', function (Blueprint $table) {
			$table->dropColumn('insurance');
			$table->dropColumn('reg_sticker');
			$table->dropColumn('experience');
			$table->dropColumn('referral_code');
		});
	}
}
