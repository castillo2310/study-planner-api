<?php


namespace StudyPlanner\Infrastructure\Controller;


use StudyPlanner\Application\Plan\Create\CreatePlanCommandHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

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
        return $this->json('hola');
    }
}
