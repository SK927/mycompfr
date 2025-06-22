<?php

  class email
  {
    public $subject; 
    public $header; 
    public $message; 
    public $sign;

    public function __construct()
    {
      $this->subject = '';
      $this->header = '';
      $this->message = '<html><body>';
    }

    public function create_header( $from, $bcc = NULL )
    {
      $bcc = $bcc ? "{$from};{$bcc}" : $from;
      $this->header  = "MIME-Version: 1.0\r\n";
      $this->header .= "Content-type: text/html; charset=utf-8\r\n";
      $this->header .= "From: MyComp\r\nBcc: [email]{$bcc}[/email]\r\n";
      $this->header .= "Reply-To: {$from}\r\nX-Mailer: PHP/" . phpversion();
    }

    public function replace_subject_text( $search, $replace_by )
    {
      $this->subject = str_replace( $search, $replace_by, $this->subject );
    }

    public function replace_message_text( $search, $replace_by )
    {
      $this->message = str_replace( $search, $replace_by, $this->message );
    }

    public function concatenate_to_message( $added_text )
    {
      $this->message .= $added_text;
    }
  }
  
?>