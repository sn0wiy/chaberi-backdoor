<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\RoomDataService;

class RecordHistoryCommand extends Command
{
    protected static $defaultName = 'app:loginhistory:record';

    private RoomDataService $roomDataService;

    public function __construct(RoomDataService $roomDataService)
    {
        $this->roomDataService = $roomDataService;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->roomDataService->addRoomData();
        $this->roomDataService->removeRoomData();
    
        return 1;
    }
}