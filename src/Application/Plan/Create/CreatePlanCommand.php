<?php


namespace StudyPlanner\Application\Plan\Create;


use DateTime;

class CreatePlanCommand
{
    private DateTime $startDate;
    private DateTime $endDate;
    private int $dailyStudyHours;
    private array $allowedWeekDays;
    private array $chapters;

    /**
     * CreatePlanCommand constructor.
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int $dailyStudyHours
     * @param array $allowedWeekDays
     * @param array $chapters
     */
    public function __construct(
        DateTime $startDate,
        DateTime $endDate,
        int $dailyStudyHours,
        array $allowedWeekDays,
        array $chapters
    ) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->dailyStudyHours = $dailyStudyHours;
        $this->allowedWeekDays = $allowedWeekDays;
        $this->chapters = $chapters;
    }

    /**
     * @return DateTime
     */
    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    /**
     * @return DateTime
     */
    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    /**
     * @return int
     */
    public function getDailyStudyHours(): int
    {
        return $this->dailyStudyHours;
    }

    /**
     * @return array
     */
    public function getAllowedWeekDays(): array
    {
        return $this->allowedWeekDays;
    }

    /**
     * @return array
     */
    public function getChapters(): array
    {
        return $this->chapters;
    }
}
