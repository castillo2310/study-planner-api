<?php


namespace StudyPlanner\Application\Plan\Create;


use StudyPlanner\Domain\Plan\PlanExportInterface;
use StudyPlanner\Domain\Plan\Services\PlanGenerator;

/**
 * Class CreatePlanCommandHandler
 * @package StudyPlanner\Application\Plan\Create
 */
class CreatePlanCommandHandler
{
    /**
     * @var PlanGenerator
     */
    private PlanGenerator $planGenerator;

    /**
     * @var PlanExportInterface
     */
    private PlanExportInterface $planExport;

    /**
     * CreatePlanCommandHandler constructor.
     * @param PlanGenerator $planGenerator
     * @param PlanExportInterface $planExport
     */
    public function __construct(PlanGenerator $planGenerator, PlanExportInterface $planExport)
    {
        $this->planGenerator = $planGenerator;
        $this->planExport = $planExport;
    }

    /**
     * @param CreatePlanCommand $command
     * @return mixed
     * @throws \Exception
     */
    public function handle(CreatePlanCommand $command)
    {
        $plan = $this->planGenerator->generate(
            $command->getStartDate(),
            $command->getEndDate(),
            $command->getDailyStudyHours(),
            $command->getAllowedWeekDays(),
            $command->getChapters()
        );

        return $this->planExport->export($plan);
    }
}
