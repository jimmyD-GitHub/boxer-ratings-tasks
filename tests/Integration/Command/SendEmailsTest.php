<?php

namespace App\Tests\Integration\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class SendEmailsTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:send-emails');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'command' => $command->getName(),
        ]);

        $output = $commandTester->getDisplay();

        $this->assertContains('Sending signupConfirmation email to billy-test@gmail.com', $output);
        $this->assertContains('Sending resetPassword email to andy-test@gmail.com', $output);
        $this->assertContains('Finished sending emails.', $output);
        $this->assertContains('Sent a total of 2 emails.', $output);

        $commandTester->execute([
            'command' => $command->getName(),
        ]);

        $output = $commandTester->getDisplay();

        //All emails have been sent
        $this->assertContains('Finished sending emails.', $output);
        $this->assertContains('Sent a total of 0 emails.', $output);
    }
}