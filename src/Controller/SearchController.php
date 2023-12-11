<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\SearchInput;
use App\Repository\ReadEventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route(path: '/api/search', name: 'api_search', methods: ['GET'])]
final class SearchController extends AbstractController
{
    public function __invoke(
        #[MapQueryString] SearchInput $searchInput,
        ReadEventRepository $repository,
        SerializerInterface $serializer
    ): JsonResponse {
        $countByType = $repository->countByType($searchInput);

        return $this->json([
            'meta' => [
                'totalEvents' => $repository->countAll($searchInput),
                'totalPullRequests' => $countByType['PR'] ?? 0,
                'totalCommits' => $countByType['COM'] ?? 0,
                'totalComments' => $countByType['MSG'] ?? 0,
            ],
            'data' => [
                'events' => $repository->getLatest($searchInput),
                'stats' => $repository->statsByTypePerHour($searchInput)
            ]
        ]);
    }
}
