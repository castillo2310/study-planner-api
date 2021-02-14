<?php


namespace StudyPlanner\Domain\StudyEvent;


use DateTime;

class StudyEvent
{
    private DateTime $date; //TODO: VALUE OBJECTS
    private int $hours;
    private string $description;
    private string $color;

    /**
     * StudyEvent constructor.
     * @param DateTime $date
     * @param int $hours
     * @param string $description
     * @param string $color
     */
    public function __construct(DateTime $date, int $hours, string $description, string $color)
    {
        $this->date = $date;
        $this->hours = $hours;
        $this->description = $description;
        $this->color = $color;
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

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }
}
