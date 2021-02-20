<?php


namespace StudyPlanner\Infrastructure\Controller;


use StudyPlanner\Application\Plan\Create\CreatePlanCommand;
use StudyPlanner\Application\Plan\Create\CreatePlanCommandHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class CreateICSPlanController
 * @package StudyPlanner\Infrastructure\Controller
 */
class CreateICSPlanController extends AbstractController
{
    /**
     * @var CreatePlanCommandHandler
     */
    private CreatePlanCommandHandler $handler;

    /**
     * CreateICSPlanController constructor.
     * @param CreatePlanCommandHandler $handlerICS
     */
    public function __construct(CreatePlanCommandHandler $handlerICS)
    {
        $this->handler = $handlerICS;
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function __invoke(Request $request)
    {
        try {
            $startDate = new \DateTime($request->get('startDate'));
            $endDate = new \DateTime($request->get('endDate'));

            $command = new CreatePlanCommand(
                $startDate,
                $endDate,
                $request->get('dailyStudyHours'),
                $request->get('allowedWeekDays'),
                $request->get('chapters')
            );

            $ics = $this->handler->handle($command);

            $response = new StreamedResponse(function () use ($ics) {
                echo $ics;
            });
            $response->headers->set('Content-Type', 'text/calendar; charset=utf-8');
            $response->headers->set('Content-Disposition', 'attachment; filename="studyPlanner.ics"');

            return $response;
        } catch (\Throwable $exception) {
            return $this->json(['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
