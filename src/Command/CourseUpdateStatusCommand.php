<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCommand(
    name: 'app:course:update-status',
    description: 'Update course status',
)]
#[AsCronTask('* * * * *')]
class CourseUpdateStatusCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        dump('Scheduler exec '. date('H:i:s d-m-Y'));
        
        // Update Json file to log actions about course status
        $path = 'public/json/courses.json';
        // Datas to log
        $data = [
            'last_update' => date('H:i:s d-m-Y'),
            'count' => $this->getExecutionCount(),
            'status' => 'success',
            'message' => 'Scheduler - Course status check : '
        ];

        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));

        $io->success('Command - Course status check : '. date('H:i:s d-m-Y'));

        return Command::SUCCESS;
    }

    private function getExecutionCount() : int {
        $path = 'public/json/courses.json';
        if (file_exists($path)) {
            // Read the JSON file
            $data = json_decode(file_get_contents($path), true);
            // Check if count exists and increment it if not, init it to 1
            return ($data['count'] ?? 0) + 1;
        }
    }
}
