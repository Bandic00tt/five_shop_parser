<?php
namespace App;

use GuzzleHttp\Client;

class Handler
{
    public const DATA_DIR = __DIR__ .'/../data/';
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function updateAndUploadResults(int $cityId, array $results)
    {
        $jsonData = json_encode($results, JSON_UNESCAPED_UNICODE);
        $fileName = $cityId .'.json';

        if (file_put_contents(self::DATA_DIR . $fileName, $jsonData)) {
            $token = $_ENV['YD_TOKEN'];
            $yandexDisk = new YandexDisk($this->client, $token, $fileName);
            $yandexDisk->uploadFile();
        }
    }
}