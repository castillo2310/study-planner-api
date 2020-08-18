<?php


namespace StudyPlanner\Domain\Plan\Services;


use DateInterval;
use DateTime;
use StudyPlanner\Domain\Plan\Plan;
use StudyPlanner\Domain\StudyEvent\StudyEvent;

/**
 * Class PlanGenerator
 * @package StudyPlanner\Domain\Plan\Services
 */
class PlanGenerator
{
    /**
     *
     */
    const RECOMMENDED_PAGES_PER_HOUR = 1.5;

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param int $dailyStudyHours
     * @param array $allowedWeekDays
     * @param array $chapters
     * @return Plan
     * @throws \Exception
     */
    public function generate(
        DateTime $startDate,
        DateTime $endDate,
        int $dailyStudyHours,
        array $allowedWeekDays,
        array $chapters
    ): Plan {
        $chapters = $this->parseChapters($chapters);
        $studyDays = $this->calculateStudyDays(
            $startDate,
            $endDate,
            $allowedWeekDays
        );

        $totalPages = $this->calculateTotalPages($chapters);
        $totalRecommendedStudyHours = $this->calculateStudyHoursFromPages($totalPages);
        $userAvailableStudyHours = $this->calculateUserAvailableStudyHours($studyDays, $dailyStudyHours);

        if ($totalRecommendedStudyHours > $userAvailableStudyHours) {
            throw new \InvalidArgumentException('User study hours cannot be less than recommended study hours');
        }

        $studyEvents = [];
        $chapterToBeAssigned = $this->getPendingChapter($chapters);
        for ($i = 0; $i < count($studyDays) && $chapterToBeAssigned; $i++) {
            $day = $studyDays[$i];
            $dayHoursToBeAssigned = $dailyStudyHours;

            while ($dayHoursToBeAssigned > 0 && $chapterToBeAssigned) {
                $hoursToBeAssigned = min($dayHoursToBeAssigned, $chapterToBeAssigned['hoursToBeAssigned']);

                $studyEvents[] = new StudyEvent($day, $hoursToBeAssigned, $chapterToBeAssigned['description']);

                $chapterToBeAssigned['hoursToBeAssigned'] -= $hoursToBeAssigned;
                $dayHoursToBeAssigned -= $hoursToBeAssigned;
                $chapterToBeAssigned = $this->getPendingChapter($chapters);
            }
        }

        return new Plan($studyEvents);
    }

    /**
     * @param array $chapters
     * @return array|null
     */
    private function getPendingChapter(array $chapters): ?array
    {
        foreach ($chapters as $chapter) {
            if ($chapter['hoursToBeAssigned'] > 0) {
                return $chapter;
            }
        }

        return null;
    }

    /**
     * @param array $chapters
     * @return array
     */
    private function parseChapters(array $chapters): array
    {
        foreach ($chapters as &$chapter) {
            if (!isset($chapter['description']) || !isset($chapter['pages'])) {
                throw new \InvalidArgumentException('Chapter description and chapter pages are required');
            }

            $chapter['hoursToBeAssigned'] = $this->calculateStudyHoursFromPages($chapter['pages']);
        }

        return $chapters;
    }

    /**
     * @param int $pages
     * @return float
     */
    private function calculateStudyHoursFromPages(int $pages): float
    {
        return ceil($pages / self::RECOMMENDED_PAGES_PER_HOUR);
    }

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @param array $allowedWeekDays
     * @return array
     * @throws \Exception
     */
    private function calculateStudyDays(\DateTime $startDate, \DateTime $endDate, array $allowedWeekDays)
    {
        $dayList = [];
        while ($startDate <= $endDate) {
            $weekDay = $startDate->format('w');
            if (in_array($weekDay, $allowedWeekDays)) {
                $dayList[] = clone $startDate;
            }
            $startDate = $startDate->add(new DateInterval('P1D'));
        }

        return $dayList;
    }

    /**
     * @param array $chapters
     * @return int
     */
    private function calculateTotalPages(array $chapters): int
    {
        $totalPages = 0;
        foreach ($chapters as $chapter) {
            $totalPages += $chapter['pages'];
        }

        return $totalPages;
    }

    /**
     * @param array $dayList
     * @param int $dailyStudyHours
     * @return float
     */
    private function calculateUserAvailableStudyHours(array $dayList, int $dailyStudyHours): float
    {
        return count($dayList) * $dailyStudyHours;
    }
}
