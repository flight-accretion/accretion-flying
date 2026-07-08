<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailContentTable extends Migration
{
	public function up() {
		Schema::create('mail_contents', function(Blueprint $table): void{
			$table->increments('id');
			$table->string('subject');
			$table->string('name', 20);
			$table->text('content'); 
			$table->timestamps();
		});
	}
	
	public function down() {
		Schema::drop('mail_contents');
	}
}
