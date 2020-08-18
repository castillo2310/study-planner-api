<?php


namespace App\Tests\Unit\Domain\Plan\Services;


use PHPUnit\Framework\TestCase;
use StudyPlanner\Domain\Plan\Plan;
use StudyPlanner\Domain\Plan\Services\PlanGenerator;

class PlanGeneratorTest extends TestCase
{
    private \DateTime $startDate;
    private \DateTime $endDate;
    private int $dailyStudyHours;
    private array $allowedWeekDays;
    private array $chapters;

    public function setUp()
    {
        $this->startDate = new \DateTime('2020-05-01');
        $this->endDate = new \DateTime('2020-05-10');
        $this->dailyStudyHours = 2;
        $this->allowedWeekDays = [1,2,3,4,5];
        $this->chapters = [
            [
                'description' => 'Chapter 1',
                'pages' => 10
            ],
            [
                'description' => 'Chapter 2',
                'pages' => 3
            ],
        ];
    }

    /**
     * @test
     */
    public function shouldReturnAPlan()
    {
        $service = new PlanGenerator();
        $plan = $service->generate(
            $this->startDate,
            $this->endDate,
            $this->dailyStudyHours,
            $this->allowedWeekDays,
            $this->chapters
        );

        $this->assertInstanceOf(Plan::class, $plan);
    }

    /**
     * @test
     */
    public function shouldReturnAPlanWithTheCorrectNumberOfStudyEvents()
    {
        $service = new PlanGenerator();
        $plan = $service->generate(
            $this->startDate,
            $this->endDate,
            $this->dailyStudyHours,
            $this->allowedWeekDays,
            $this->chapters
        );

        $this->assertInstanceOf(Plan::class, $plan);
        $this->assertCount(6, $plan->getStudyEvents());
    }

    /**
     * @test
     */
    public function shouldThrowErrorWhenRecommendedStudyHoursAreGreaterThanUserAvailableHours()
    {
        $this->expectException(\Exception::class);

        $service = new PlanGenerator();
        $service->generate(
            $this->startDate,
            new \DateTime('2020-05-02'),
            $this->dailyStudyHours,
            $this->allowedWeekDays,
            $this->chapters
        );
    }
}
