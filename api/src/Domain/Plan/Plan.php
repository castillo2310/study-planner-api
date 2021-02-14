<?php


namespace StudyPlanner\Domain\Plan;


class Plan
{
    private array $studyEvents;

    /**
     * Plan constructor.
     * @param array $studyEvents
     */
    public function __construct(array $studyEvents)
    {
        $this->studyEvents = $studyEvents;
    }

    /**
     * @return array
     */
    public function getStudyEvents(): array
    {
        return $this->studyEvents;
    }
}
