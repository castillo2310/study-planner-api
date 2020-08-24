<?php


namespace StudyPlanner\Infrastructure\Controller;


use StudyPlanner\Application\Plan\Create\CreatePlanCommand;
use StudyPlanner\Application\Plan\Create\CreatePlanCommandHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CreatePlanController extends AbstractController
{
    /**
     * @var CreatePlanCommandHandler
     */
    private CreatePlanCommandHandler $handler;

    /**
     * CreatePlanController constructor.
     * @param CreatePlanCommandHandler $handler
     */
    public function __construct(CreatePlanCommandHandler $handler)
    {
        $this->handler = $handler;
    }

    public function __invoke(Request $request)
    {
        $command = new CreatePlanCommand(
            new \DateTime('2020-05-01'),
            new \DateTime('2020-05-10'),
            2,
            [1,2,3,4,5],
            [
                [
                    'description' => 'Chapter 1',
                    'pages' => 10
                ],
                [
                    'description' => 'Chapter 2',
                    'pages' => 3
                ],
            ]
        );

        $response = $this->handler->handle($command);

        return new Response(
            $response,
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf'
            ]
        );
    }
}
