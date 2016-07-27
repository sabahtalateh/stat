<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Predis\Client as Predis;
use Predis\Pipeline\Pipeline;

class SeedRedis extends Command
{
    private $date = [
        '2016-01-01',
        '2016-01-02',
        '2016-01-03',
        '2016-01-04',
        '2016-01-05',
    ];

    private $os = [
        'Win7',
        'Win8',
        'Win10',
        'WinXP',
        'Android4.4',
        'Android5.2',
        'MacOS10.10',
        'MacOS10.9',
        'Linuxx86',
        'Linuxx64',
        'iOS9',
        'iOS8'
    ];

    private $page = [
        'main',
        'about',
        'products',
        'how-to-feed-the-ogre'
    ];

    private $browser = [
        'IE7',
        'IE8',
        'IE9',
        'Crome',
        'Firefox',
        'Safari',
    ];

    private $geo = [
        'GB',
        'RU',
        'US',
        'UA',
        'KZ'
    ];

    private $ref = [
        'google.com',
        'ya.ru',
        'sergey.shnurov.ru'
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:seed 
    {--amount=10000 : The amount of records to seed (integer)}
    {--chunk-size=25000 : The amount of chunk to seed}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seeding redis with test statistic data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param Predis $predis
     * @return mixed
     */
    public function handle(Predis $predis)
    {
        $amount = $this->option('amount');

        if (!ctype_digit($amount)) {
            $this->error('Amount should be an integer');
            return;
        }

        $chunkSize = $this->option('chunk-size');

        if (!ctype_digit($chunkSize)) {
            $this->error('Chunk size should be an integer');
            return;
        }

        $chunkSize = (int)$chunkSize;
        $from = 0;
        $to = $chunkSize;
        $total = $amount;

        $progress = $this->output->createProgressBar($amount);
        $progress->setFormat('Progress: %percent%% %memory:6s% in use');

        $start = microtime(true);

        $faker = \Faker\Factory::create();
        while ($from < $total) {

            if ($to > $total)
                $to = $total;

            $predis->pipeline(function (Pipeline $pipe) use ($from, $to, $progress, $faker) {
                for ($i = $from; $i < $to; $i++) {

                    $page = $this->page[array_rand($this->page)];
                    $geo = $this->geo[array_rand($this->geo)];
                    $browser = $this->browser[array_rand($this->browser)];
                    $os = $this->os[array_rand($this->os)];
                    $ref = $this->ref[array_rand($this->ref)];

                    $cookie = md5(random_int(1, 10000));

                    $randIP = "" . mt_rand(0, 255) . "." . mt_rand(0, 255) . "." . mt_rand(0, 255) . "." . mt_rand(0, 255);

                    $pipe->set("lookup:page={$page}:hits", random_int(1, 200));
                    $pipe->sadd("lookup:page={$page}:cookie", "$cookie:{$this->randDate('2016-01-01', '2016-02-01')}");
                    $pipe->sadd("lookup:page={$page}:ip", "$randIP:{$this->randDate('2016-01-01', '2016-02-01')}");

                    $pipe->set("lookup:browser={$browser}:hits", random_int(1, 200));
                    $pipe->sadd("lookup:browser={$browser}:cookie", "$cookie:{$this->randDate('2016-01-01', '2016-02-01')}");
                    $pipe->sadd("lookup:browser={$browser}:ip", "$randIP:{$this->randDate('2016-01-01', '2016-02-01')}");

                    $pipe->set("lookup:os={$os}:hits", random_int(1, 200));
                    $pipe->sadd("lookup:os={$os}:cookie", "$cookie:{$this->randDate('2016-01-01', '2016-02-01')}");
                    $pipe->sadd("lookup:os={$os}:ip", "$randIP:{$this->randDate('2016-01-01', '2016-02-01')}");

                    $pipe->set("lookup:geo={$geo}:hits", random_int(1, 200));
                    $pipe->sadd("lookup:geo={$geo}:cookie", "$cookie:{$this->randDate('2016-01-01', '2016-02-01')}");
                    $pipe->sadd("lookup:geo={$geo}:ip", "$randIP:{$this->randDate('2016-01-01', '2016-02-01')}");

                    $pipe->set("lookup:ref={$ref}:hits", random_int(1, 200));
                    $pipe->sadd("lookup:ref={$ref}:cookie", "$cookie:{$this->randDate('2016-01-01', '2016-02-01')}");
                    $pipe->sadd("lookup:ref={$ref}:ip", "$randIP:{$this->randDate('2016-01-01', '2016-02-01')}");


//                    $pipe->hmset(
//                        "{$this->randDate('2016-01-01', '2016-02-01')}:{$this->page[array_rand($this->page)]}:{$this->os[array_rand($this->os)]}:{$this->browser[array_rand($this->browser)]}:{$this->geo[array_rand($this->geo)]}:{$this->ref[array_rand($this->ref)]}", [
//                            'hit' => random_int(1, 8000),
//                            'uniqueIp' => random_int(1, 1000),
//                            'uniqueCookie' => random_int(1, 1000)
//                        ]
//                    );
//                    $date = $this->randDate('2014-01-01', '2016-01-01');
//
//                    $hit = random_int(1, 8000);
//                    $uniqueIp = random_int(1, 1000);
//                    $uniqueCookie = random_int(1, 1000);
//                    $page = $this->page[array_rand($this->page)];
//
//                    $pipe->hmset("pg={$page}:br={$this->browser[array_rand($this->browser)]}", [
//                        'hit' => $hit,
//                        'ip' => $uniqueIp,
//                        'cookie' => $uniqueCookie
//                    ]);
//
//                    $pipe->hmset("pg={$page}:os={$this->os[array_rand($this->os)]}", [
//                        'hit' => $hit,
//                        'ip' => $uniqueIp,
//                        'cookie' => $uniqueCookie
//                    ]);
//
//                    $pipe->hmset("pg={$page}:geo={$this->geo[array_rand($this->geo)]}", [
//                        'hit' => $hit,
//                        'ip' => $uniqueIp,
//                        'cookie' => $uniqueCookie
//                    ]);
//
//                    $pipe->hmset("pg={$page}:ref={$this->ref[array_rand($this->ref)]}", [
//                        'hit' => $hit,
//                        'ip' => $uniqueIp,
//                        'cookie' => $uniqueCookie
//                    ]);

                }
            });
            $progress->advance($chunkSize);

            $from += $chunkSize;
            $to += $chunkSize;
        }
        $progress->finish();
        $this->info(PHP_EOL . "{$amount} row was seeded in " . round((microtime(true) - $start), 2) . " ms.");
    }

    private function randDate($minDate, $maxDate)
    {
        $minEpoch = strtotime($minDate);
        $maxEpoch = strtotime($maxDate);

        $randEpoch = rand($minEpoch, $maxEpoch);

        return date('Y-m-d_H:i:s', $randEpoch);
    }
}
