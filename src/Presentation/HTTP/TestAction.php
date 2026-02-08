<?php

namespace App\Presentation\HTTP;

use App\Application\Service\SeederService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final readonly class TestAction
{
    public function __construct(
        private SeederService $seederService,
    ) {
    }

    #[Route('/api/test', name: 'api_test', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $course = $this->seederService->createEntitiesFakeChain();

        return new JsonResponse($course->toArray(), 201);
    }
}
