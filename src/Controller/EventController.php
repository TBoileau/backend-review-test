<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\EventInput;
use App\Entity\Event;
use App\Repository\WriteEventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/event/{id}/update', name: 'api_commit_update', methods: ['PUT'])]
final class EventController extends AbstractController
{
    public function __invoke(
        #[MapRequestPayload] EventInput $eventInput,
        Event $event,
        WriteEventRepository $writeEventRepository
    ): Response {
        $writeEventRepository->update($eventInput, $event->id());

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
