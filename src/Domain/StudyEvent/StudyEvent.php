<?php


namespace StudyPlanner\Domain\StudyEvent;


use DateTime;

class StudyEvent
{
    private DateTime $date;
    private int $hours;
    private string $description;

    /**
     * StudyEvent constructor.
     * @param DateTime $date
     * @param int $hours
     * @param string $description
     */
    public function __construct(DateTime $date, int $hours, string $description)
    {
        $this->date = $date;
        $this->hours = $hours;
        $this->description = $description;
    }

    /**
     * @return DateTime
     */
    public function getDate(): DateTime
    {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getHours(): int
    {
        return $this->hours;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
