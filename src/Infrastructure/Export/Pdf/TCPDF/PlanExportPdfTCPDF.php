<?php


namespace StudyPlanner\Infrastructure\Export\Pdf\TCPDF;


use StudyPlanner\Domain\Plan\Plan;
use StudyPlanner\Domain\Plan\PlanExportPdfInterface;

class PlanExportPdfTCPDF implements PlanExportPdfInterface
{
    private CustomTCPDF $pdf;

    public function __construct()
    {
        $this->pdf = new CustomTCPDF('L', 'mm', 'A4');
        $this->pdf->SetMargins(20, 10);
    }

    public function export($plan)
    {
        if (!$plan instanceof Plan) {
            throw new \InvalidArgumentException('Plan is required');
        }

        $events = $this->getStudyEventsGroupedByMonths($plan->getStudyEvents());
        foreach ($events as $month => $studyEvents) {
            $monthName = \DateTime::createFromFormat('!m', $month)->format('F');
            $this->drawMonthName($monthName);
            $this->drawWeekDays();
            $this->drawMonthDays($studyEvents);
        }

        return $this->pdf->Output('doc.pdf');
    }

    private function drawMonthDays(array $studyEvents)
    {
        $firstEventDate = \DateTimeImmutable::createFromMutable($studyEvents[0]->getDate());
        $firstDayOfMonth = $firstEventDate->modify('first day of this month');
        $lastDayOfMonth = $firstEventDate->modify('last day of this month');


        $weekList = [];
        $date = \DateTime::createFromImmutable($firstDayOfMonth);
        while ($date <= $lastDayOfMonth) {
            $week = [];
            $firstDayOfWeek = \DateTimeImmutable::createFromMutable($date)->modify('monday this week');
            $lastDayOfWeek = $firstDayOfWeek->modify('+6 day');

            $date = \DateTime::createFromImmutable($firstDayOfWeek);
            while ($date <= $lastDayOfWeek) {
                $week[] = \DateTimeImmutable::createFromMutable($date);
                $date->modify('+1 day');
            }

            $weekList[] = $week;
        }

        $this->pdf->setFontSpacing(0);
        $this->pdf->setCellPaddings('', 2);
        $this->pdf->SetFont('helvetica', '', 9);
        foreach ($weekList as $week) {
            for ($i=0;$i<sizeof($week);$i++){
                $day = $week[$i];
                $border = 1;
                if ($i === 0 ) {
                    $border = 'TRB';
                } else if ($i === sizeof($week) - 1) {
                    $border = 'TLB';
                }

                $html = '<div style="text-align:right;">'.$day->format('d').'</div>';
                $html .= '<table style="padding:1px;border-spacing: 2px;">';
                foreach ($studyEvents as $studyEvent) {
                    if ($studyEvent->getDate()->format('Y-m-d') == $day->format('Y-m-d')) {
                        $html .= '<tr><td style="background-color: '.$studyEvent->getColor().';color:white;">'.$studyEvent->getDescription().'</td></tr>';
                    }
                }
                $html .= '</table>';
                $this->pdf->WriteHTMLCell(36.71, 25, '', '', $html, $border);
            }
            $this->pdf->Ln();
        }
    }

    private function drawWeekDays()
    {
        $weekDays = ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'];

        $this->pdf->SetFont('helveticaB', 'B', 10);
        foreach ($weekDays as $weekDay) {
            $this->pdf->Cell(36.71, 10, $weekDay, 'TB', 0, 'C');
        }
        $this->pdf->Ln();
    }

    private function drawMonthName(string $monthName): void
    {
        $this->pdf->AddPage();
        $this->pdf->SetFont('helveticaB', 'B', 40);
        $this->pdf->setFontSpacing(5);
        $this->pdf->SetTextColor(0, 88, 133);
        $this->pdf->SetLineStyle(['color' => [0,88,133]]);
        $this->pdf->setCellPaddings('', '');
        $this->pdf->Cell(0, 30, mb_strtoupper($monthName, 'UTF-8'),0,0, 'C');
        $this->pdf->Ln();
    }

    private function getStudyEventsGroupedByMonths(array $studyEvents): array
    {
        $days = [];
        foreach ($studyEvents as $studyEvent) {
            $date = $studyEvent->getDate();
            $month = $date->format('n');

            $days[$month][] = $studyEvent;
        }

        return $days;
    }

}
