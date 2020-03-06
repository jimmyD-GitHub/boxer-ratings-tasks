# boxer-ratings-tasks
A cron server that runs jobs needed to facilitate BoxerRatings.com

## Scheduled Jobs
There are currently only two scheduled jobs.

### Refresh Ratings
This job runs every 5 minutes to recalculate the overall rating points assigned to boxers listed on the 
BoxerRatings.com homepage:

```shell script
$ php bin/console app:refresh-ratings
Updating boxer ratings for the heavyweight division...
Finished updating boxer ratings for the heavyweight division.
Updating boxer ratings for the cruiserweight division...
No ratings updated for the cruiserweight division!
Updating boxer ratings for the light-heavyweight division...
No ratings updated for the light-heavyweight division!
Updating boxer ratings for the super-middleweight division...
No ratings updated for the super-middleweight division!
Updating boxer ratings for the middleweight division...
No ratings updated for the middleweight division!
Updating boxer ratings for the light-middleweight division...
```

### Send Emails
This job runs once a minute and checks the email queue table in the database for emails that need to be sent to users.
These can be sign-up verification emails or password reset request emails:

```shell script
$ php bin/console app:send-emails
Finished sending emails.
Sent a total of 2 emails.
```

### Send Test Email
This command is used locally to verify the mailer configuration and check the look and feel of the HTML emails sent.

The MAILER_DSN environment variable should be set using a .env.dev.local file to send test emails:

```shell script
$ php bin/console app:send-test-email test-recipient@gmail.com
Sending test email...
Email sent.
```

## Configuration
This is a symfony CLI application that requires the following environment variables:

```.dotenv
# Typical development environment variables...

APP_ENV=dev
APP_SECRET=61a46ccf7636cdb7eec66f67b828b193
DATABASE_URL=mysql://root@mysql:3306/boxers
UI_BASE_URL=http://localhost:3000
```
The UI_BASE_URL variable is used to set the web application URL's in automated emails.

It is important that the crontab service is also able to access these environment variables. In the development
environment this is achieved by writing them to a config file:

```shell script
$ printenv | sed 's/^\(.*\)$/\1/g' > /etc/environment
```

## Development
This application acts a cron job server for the Docker development environment. 

Use docker-compose to build and run it from your docker host:

```shell script
$ docker-compose up --build -d
```

To push a rebuilt version of the image just do:

```shell script
$ docker push jimmydockerhub/boxer-ratings-tasks:latest
```

## Tests

```shell script
$ php bin/phpunit

 // Clearing the cache for the test environment with debug true

 [OK] Cache for the "test" environment (debug=true) was successfully cleared.

Loading DB test data fixtures...

   > purging database
   > loading App\DataFixtures\AppFixtures

PHPUnit 7.5.18 by Sebastian Bergmann and contributors.

Testing Project Test Suite
.....                                                               5 / 5 (100%)

Time: 16.56 seconds, Memory: 34.00 MB

OK (5 tests, 43 assertions)
```

## Production

The commands are executed using an AWS Lambda function and get run at scheduled times using Cloud Watch. This is all
configured using the serverless.yml file with bref.

In production the MAILER_DSN environment variable needs to be set for the SMTP emails, sent via Amazon SES.

To do a production build and update the AWS stack do:

```shell script
$ composer app-deploy
```

If your local environment has access to the required AWS credentials configuration you can executed the commands
like this:

```shell script
$ vendor/bin/bref cli tasks-dev-console -- app:send-emails
$ vendor/bin/bref cli tasks-dev-console -- app:refresh-ratings
```