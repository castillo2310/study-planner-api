<?php


namespace StudyPlanner\Infrastructure\Export\Pdf\TCPDF;


class CustomTCPDF extends \TCPDF
{
    public function Header() {
        // get the current page break margin
        $bMargin = $this->getBreakMargin();
        // get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;
        // disable auto-page-break
        $this->SetAutoPageBreak(false, 0);
        // set bacground image
        $img_file = '/application/assets/img/plan-background.jpg';
        $this->Image($img_file, 0, 0, 297, 210, '', '', '', true, 300, '', false, false, 0);
        // restore auto-page-break status
        $this->SetAutoPageBreak($auto_page_break, $bMargin);
        // set the starting point for the page content
        $this->setPageMark();
    }
}
