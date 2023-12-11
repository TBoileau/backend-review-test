<?php

namespace App\Controller;

use App\Controller\AbstractControllertractController as AbstractControllertractControllerAlias;
use App\Dto\SearchInput;
use App\Repository\ReadEventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class SearchController extends AbstractController
{
    private ReadEventRepository $repository;
    private SerializerInterface $serializer;

    public function __construct(
        ReadEventRepository $repository,
        SerializerInterface  $serializer
    ) {
        $this->repository = $repository;
        $this->serializer = $serializer;
    }

    /**
     * @Route(path="/api/search", name="api_search", methods={"GET"})
     */
    public function searchCommits(#[MapQueryString] SearchInput $searchInput): JsonResponse
    {
        $countByType = $this->repository->countByType($searchInput);

        $this->json([
            'meta' => [
                'totalEvents' => $this->repository->countAll($searchInput),
                'totalPullRequests' => $countByType['pullRequest'] ?? 0,
                'totalCommits' => $countByType['commit'] ?? 0,
                'totalComments' => $countByType['comment'] ?? 0,
            ],
            'data' => [
                'events' => $this->repository->getLatest($searchInput),
                'stats' => $this->repository->statsByTypePerHour($searchInput)
            ]
        ]);
    }
}
