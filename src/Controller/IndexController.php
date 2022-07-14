<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\RoomDataService;
use App\Service\DateService;

class IndexController extends AbstractController
{
    private RoomDataService $roomDataService;

    private DateService $dateService;

    public function __construct(
        RoomDataService $roomDataService,
        DateService $dateService
    )
    {
        $this->roomDataService = $roomDataService;
        $this->dateService = $dateService;
    }

    /**
     *    @Route("/", name="index")
     */
    public function index(Request $req): Response
    {
        $ago = $req->query->get('ago');
        $hour = $this->dateService->hourNormalizer($ago);

        $pages = $this->roomDataService->getRoomHistories($hour);
        
        return $this->render('index.html.twig', [
            'pages' => $pages
        ]);
    }
}