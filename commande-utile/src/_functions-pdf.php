<?php

  require_once dirname( __DIR__, 2 ) . '/src/tcpdf/tcpdf.php';

  class CUPDF extends TCPDF
  {
    public function WriteLine( $html )
    {
      $this->startTransaction(); 
      $start_page = $this->getPage();                       
      $this->writeHTMLCell( 0, 0, '', '', $html, 0, 1, false, true, 'L'  );
      $end_page = $this->getPage();
      
      if  ($end_page != $start_page)
      {
        $this->rollbackTransaction( true ); 
        $this->AddPage();
        $this->writeHTMLCell( 0, 0, '', '', $html, 0, 1, false, true, 'L'  );
      }
      else
      {
        $this->commitTransaction();     
      } 
    }
  }


  /**
   * create_new_pdf(): create new PDF reference
   * @return (pdf) the PDF reference created
   */

  function create_new_pdf( $competition_name )
  {
    $pdf = new CUPDF( PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false );
    $pdf->setCreator( PDF_CREATOR );
    $pdf->setAuthor( PDF_AUTHOR );
    $pdf->SetHeaderData( dirname( __DIR__, 1 ) . "/assets/img/favicon.png", PDF_HEADER_LOGO_WIDTH, ' Commande Utile', "  » {$competition_name}", array( 0,0,0 ), array( 50,10,10 ) );
    $pdf->setHeaderFont( array( PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN ) );
    $pdf->setFooterFont( array( PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_MAIN ) );
    $pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );
    $pdf->SetMargins( PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT );
    $pdf->SetHeaderMargin( PDF_MARGIN_HEADER );
    $pdf->SetFooterMargin( PDF_MARGIN_FOOTER );
    $pdf->setAutoPageBreak( TRUE, PDF_MARGIN_BOTTOM );
    $pdf->setImageScale( PDF_IMAGE_SCALE_RATIO );
    $pdf->setFontSubsetting( true );
    $pdf->setFont( 'dejavusanscondensed', '', 10, '', true );

    return $pdf;
  }

?>