<?php

namespace StudyPlanner\Infrastructure\Export\ICS\iCal;


use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\ValueObject\DateTime;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Domain\ValueObject\UniqueIdentifier;
use Eluceo\iCal\Presentation\Component;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;
use StudyPlanner\Domain\Plan\Plan;
use StudyPlanner\Domain\Plan\PlanExportICSInterface;
use StudyPlanner\Domain\StudyEvent\StudyEvent;

/**
 * Class PlanExportICSiCal
 * @package StudyPlanner\Infrastructure\Export\ICS\iCal
 */
class PlanExportICSiCal implements PlanExportICSInterface
{
    /**
     * @param mixed $plan
     * @return Component|mixed
     */
    public function export($plan)
    {
        if (!$plan instanceof Plan) {
            throw new \InvalidArgumentException('Plan is required');
        }

        $calendar = new Calendar();
        foreach ($plan->getStudyEvents() as $studyEvent) {
            $event = $this->generateEvent($studyEvent);
            $calendar->addEvent($event);
        }



        $calendarFactory = new CalendarFactory();

        return $calendarFactory->createCalendar($calendar);
    }

    /**
     * @param StudyEvent $studyEvent
     * @return Event
     */
    private function generateEvent(StudyEvent $studyEvent): Event
    {
        $uniqueId = 'studyPlanner'.uniqid().$studyEvent->getDate()->getTimestamp();
        $uniqueIdentifier = new UniqueIdentifier($uniqueId);

        $eventStart = $studyEvent->getDate();
        $eventEnd = \DateTimeImmutable::createFromMutable($eventStart)->modify('+'.$studyEvent->getHours().' hours');

        $start = new DateTime($eventStart, false);
        $end = new DateTime($eventEnd, false);
        $occurrence = new TimeSpan($start, $end);

        $event = new Event($uniqueIdentifier);
        $event
            ->setDescription($studyEvent->getDescription())
            ->setSummary($studyEvent->getDescription())
            ->setOccurrence($occurrence);

        return $event;
    }
}
