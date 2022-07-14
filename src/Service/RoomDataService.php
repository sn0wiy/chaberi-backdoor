<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\RoomLoginHistory;
use App\Repository\RoomLoginHistoryRepository;
use App\Service\DateService;

class RoomDataService
{
    private EntityManagerInterface $entityManager;

    private DateService $dateService;

    private RoomLoginHistoryRepository $roomLoginHistoryRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        DateService $dateService,
        RoomLoginHistoryRepository $roomLoginHistoryRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->dateService = $dateService;
        $this->roomLoginHistoryRepository = $roomLoginHistoryRepository;
    }

    public function getRoomHistories(int $hour): array
    {
        $date = $this->dateService->calcPastDate($hour);
        $histories = $this->roomLoginHistoryRepository->findBy(['created' => $date]);

        $result = [];

    foreach ($histories as $history) {
        $page = $history->getPage();
        $result[$page][] = $history;
    }
        
        return $result;
    }

    public function getRoomData(): array
    {
        $page_uri = [
            'https://www.chaberi.com/1',
            'https://www.chaberi.com/2',
            'https://www.chaberi.com/3'
        ];

        $room_uri = [
            'https://www.chaberi.com/room/1',
            'https://www.chaberi.com/room/2',
            'https://www.chaberi.com/room/3'
        ];

        $room_min_id = 101;
        $page_max = count($page_uri);
        $room_max = 66;

        $dom = new \DOMDocument;
        $xpath_base = "//div[@class='tab-content']//div//div[1]//div[@class='room col-sm-6']";

        $data = [];

        for($i=0; $i<$page_max; $i++) {
            @$dom->loadHTMLFile($page_uri[$i]);
        
            $xpath =  new \DOMXPath($dom);
            $room_names = $xpath->query("${xpath_base}//span[@class='roomtitle']");
            $room_members = $xpath->query("${xpath_base}//span[@class='member roommember']");

            $page = $i + 1;

            for($j=0; $j<$room_max; $j++) {
                $id = $room_min_id + $j;
                $uri = "${room_uri[$i]}/${id}";

                $title = $room_names->item($j)->textContent;
                $members = $room_members->item($j)->textContent;

                $members =  preg_replace('/^[\p{C}\p{Z}]++|[\p{Z}]++$/u', '', $members);
                
                $data[$i][$j] = [
                    'uri' => $uri,
                    'page' => $page,
                    'title' => $title,
                    'members' => $members
                ];
             }
        }
        
        return $data;
    }

    public function addRoomData(): void
    {
        $page = $this->getRoomData();
        $date = $this->dateService->getDate();

        foreach($page as $rooms) {
            foreach($rooms as $room) {
                $history = new RoomLoginHistory();

                $history->setPage($room['page']);
                $history->setUri($room['uri']);
                $history->setTitle($room['title']);
                $history->setMembers($room['members']);
                $history->setCreated($date);

                $this->entityManager->persist($history);
            }
        }

        $this->entityManager->flush();
    }

    public function removeRoomData(): void
    {
        $deleteDate = $this->dateService->getDeleteDate();
        $trash = $this->roomLoginHistoryRepository->findBy(['created' => $deleteDate]);

        foreach($trash as $history) {
            $this->entityManager->remove($history);
        }

        $this->entityManager->flush();
    }
}