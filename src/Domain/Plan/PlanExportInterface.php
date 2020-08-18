<?php


namespace StudyPlanner\Domain\Plan;


interface PlanExportInterface
{
    public function export(Plan $plan);
}
