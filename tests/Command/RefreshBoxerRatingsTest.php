<?php

namespace App\Tests\Command;

use App\Command\RefreshBoxerRatings;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class RefreshBoxerRatingsTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:refresh-ratings');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'command'  => $command->getName(),
        ]);

        $output = $commandTester->getDisplay();

        $this->assertContains('No ratings updated for the light-flyweight division!', $output);
    }
}