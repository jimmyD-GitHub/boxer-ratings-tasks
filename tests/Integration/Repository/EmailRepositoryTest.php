<?php

namespace App\Tests\Integration\Repository;

use App\Entity\Email;
use App\Entity\User;
use App\Repository\EmailRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase as TestCase;

class EmailRepositoryTest extends TestCase
{
    /** @var EntityManager */
    private $entityManager;

    /** @var EmailRepository */
    private $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->repository = $this->entityManager->getRepository(Email::class);
    }

    public function testFindingEmails(): void
    {
        $emails = $this->repository->findBy([], ['id' => 'ASC']);

        $this->assertIsArray($emails);
        $this->assertCount(3, $emails);

        [$email1, $email2, $email3] = $emails;

        $this->assertNull($email1->getSentAt());
        $this->assertNull($email2->getSentAt());
        $this->assertEquals('2019-11-02 01:03:45', $email3->getSentAt()->format('Y-m-d H:i:s'));

        $andy = $email1->getUser();
        $billy = $email2->getUser();
        $tommy = $email3->getUser();

        $this->assertInstanceOf(User::class, $andy);
        $this->assertInstanceOf(User::class, $billy);
        $this->assertInstanceOf(User::class, $tommy);

        $this->assertEquals(1000, $andy->getId());
        $this->assertEquals('Andy', $andy->getName());
        $this->assertEquals('andy-test@gmail.com', $andy->getEmail());

        $this->assertEquals(996, $billy->getId());
        $this->assertEquals('Billy', $billy->getName());
        $this->assertEquals('billy-test@gmail.com', $billy->getEmail());

        $this->assertEquals(999, $tommy->getId());
        $this->assertEquals('Tommy', $tommy->getName());
        $this->assertEquals('tommy-test@gmail.com', $tommy->getEmail());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}