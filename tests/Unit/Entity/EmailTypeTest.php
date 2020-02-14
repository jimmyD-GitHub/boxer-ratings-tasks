<?php

namespace App\Tests\Unit\Entity;

use App\Entity\EmailType\ResetPassword;
use App\Entity\EmailType\SignupConfirmation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase as TestCase;

class EmailTypeTest extends TestCase
{
    /** @var string */
    private $baseUrl = 'https://ui.boxeratings.com';

    /** @var string */
    private $token = 'vg4JPwDpaFlzm+hXfuxpyWyhj7YqHkHq6G+hUifqlAnWu2DuPHdOZoJfYGJlMgv8YEZaqEEfwVe48dfx';

    /** @var ResetPassword */
    private $resetPasswordEmail;

    /** @var SignupConfirmation */
    private $signupConfirmationEmail;

    public function setUp(): void
    {
        $this->resetPasswordEmail = new ResetPassword($this->baseUrl, $this->token);
        $this->signupConfirmationEmail = new SignupConfirmation($this->baseUrl, $this->token);
    }

    public function testResetPasswordEmail(): void
    {
        $expected = $this->baseUrl . '/reset-password/' . urlencode($this->token);
        $this->assertEquals($expected, $this->resetPasswordEmail->getLinkUrl());

        $this->assertEquals('resetPassword.html.twig', $this->resetPasswordEmail->getHtmlTemplate());
        $this->assertEquals('resetPassword.txt.twig', $this->resetPasswordEmail->getTextTemplate());

        $subject = 'Reset Your BoxerRatings.com Password';
        $this->assertEquals($subject, $this->resetPasswordEmail->getSubject());
    }

    public function testSignupConfirmationEmail(): void
    {
        $expected = $this->baseUrl . '/verify-email/' . urlencode($this->token);
        $this->assertEquals($expected, $this->signupConfirmationEmail->getLinkUrl());

        $this->assertEquals('signupConfirmation.html.twig', $this->signupConfirmationEmail->getHtmlTemplate());
        $this->assertEquals('signupConfirmation.txt.twig', $this->signupConfirmationEmail->getTextTemplate());

        $subject = 'Confirm Your Sign Up To BoxerRatings.com';
        $this->assertEquals($subject, $this->signupConfirmationEmail->getSubject());
    }
}