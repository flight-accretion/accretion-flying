<?php

namespace FlyingCalculation\Console\Commands;

use Illuminate\Console\Command;
use FlyingCalculation\Http\Controllers\MailContentController;

class PointsUpdate extends Command
{
	protected $signature = 'pointsemail';

	protected $description = 'Command used to send emails for points summary';

	public function __construct(MailContentController $mail_content_controller)
	{
		parent::__construct();
		$this->mail_content_controller = $mail_content_controller;
	}


	public function handle()
	{
		$this->mail_content_controller->getSendMail();
		$this->line("Mails sent successfully");  
	}
}
   