<?php
	namespace App\Exceptions;
	class ExceptionException extends \Exception{
		public function __construct($exception){
			$this->message  = $exception->message;
			$this->trace    = $exception->trace;
		    $this->string   = $exception->string;
		    $this->code     = $exception->code;
		    $this->file     = $exception->file;
		    $this->line     = $exception->line;
		    $this->previous = $exception->previous;
		    parent::__contruct();
		}
	}
