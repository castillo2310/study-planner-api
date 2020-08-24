<?php


namespace StudyPlanner\Application\Plan\Create;


use DateInterval;
use StudyPlanner\Application\Plan\Export\PlanExporter;
use StudyPlanner\Domain\Plan\Plan;
use StudyPlanner\Domain\Plan\Services\PlanGenerator;
use StudyPlanner\Domain\StudyEvent\StudyEvent;

class CreatePlanCommandHandler
{
    /**
     * @var PlanGenerator
     */
    private PlanGenerator $planGenerator;

    /**
     * @var PlanExporter
     */
    private PlanExporter $planExporter;

    /**
     * CreatePlanCommandHandler constructor.
     * @param PlanGenerator $planGenerator
     * @param PlanExporter $planExporter
     */
    public function __construct(
        PlanGenerator $planGenerator,
        PlanExporter $planExporter
    ) {
        $this->planGenerator = $planGenerator;
        $this->planExporter = $planExporter;
    }

    public function handle(CreatePlanCommand $command)
    {
        $plan = $this->planGenerator->generate(
            $command->getStartDate(),
            $command->getEndDate(),
            $command->getDailyStudyHours(),
            $command->getAllowedWeekDays(),
            $command->getChapters()
        );

        return $this->planExporter->export($plan);
    }
}
