<?php

namespace App\Tests\Integration\Command;

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
            'command' => $command->getName(),
        ]);

        $output = $commandTester->getDisplay();

        $this->assertContains('Finished updating boxer ratings for the heavyweight division.', $output);
        $this->assertContains('No ratings updated for the cruiserweight division!', $output);
        $this->assertContains('No ratings updated for the light-heavyweight division!', $output);
        $this->assertContains('No ratings updated for the super-middleweight division!', $output);
        $this->assertContains('Finished updating boxer ratings for the middleweight division.', $output);
        $this->assertContains('No ratings updated for the light-middleweight division!', $output);
        $this->assertContains('No ratings updated for the welterweight division!', $output);
        $this->assertContains('No ratings updated for the light-welterweight division!', $output);
        $this->assertContains('No ratings updated for the lightweight division!', $output);
        $this->assertContains('No ratings updated for the super-featherweight division!', $output);
        $this->assertContains('No ratings updated for the featherweight division!', $output);
        $this->assertContains('No ratings updated for the super-bantamweight division!', $output);
        $this->assertContains('No ratings updated for the bantamweight division!', $output);
        $this->assertContains('No ratings updated for the super-flyweight division!', $output);
        $this->assertContains('No ratings updated for the flyweight division!', $output);
        $this->assertContains('No ratings updated for the light-flyweight division!', $output);
        $this->assertContains('No ratings updated for the minimumweight division!', $output);
    }
}