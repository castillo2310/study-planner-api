<?php


namespace StudyPlanner\Application\Plan\Export;


use StudyPlanner\Domain\Plan\Plan;
use StudyPlanner\Domain\Plan\PlanExportInterface;

/**
 * Class PlanExporter
 * @package StudyPlanner\Application\Plan\Export
 */
class PlanExporter
{
    /**
     * @var PlanExportInterface
     */
    private PlanExportInterface $planExport;

    /**
     * PlanExporter constructor.
     * @param PlanExportInterface $planExport
     */
    public function __construct(PlanExportInterface $planExport)
    {
        $this->planExport = $planExport;
    }

    /**
     * @param Plan $plan
     * @return mixed
     */
    public function export(Plan $plan)
    {
        return $this->planExport->export($plan);
    }
}
