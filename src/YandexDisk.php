<?php
namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class YandexDisk
{
    private const REMOTE_DIR = 'projects/discount/';
    private const ROOT_URL = 'https://cloud-api.yandex.net/v1/disk/resources';
    private const GET_UPLOAD_LINK = '/upload';

    public Client $client;
    public string $token;
    public string $fileName;

    /**
     * YandexDisk constructor.
     */
    public function __construct(Client $client, string $token, string $fileName)
    {
        $this->client = $client;
        $this->token = $token;
        $this->fileName = $fileName;
    }

    /**
     * @return string
     * @throws GuzzleException
     */
    public function uploadFile(): string
    {
        $uploadLink = $this->getLink(self::GET_UPLOAD_LINK);

        $res = $this->client->request('PUT', $uploadLink, [
            'body' => fopen(Handler::DATA_DIR . $this->fileName, 'rb'),
        ]);

        return (string)$res->getStatusCode();
    }

    /**
     * @param string $apiLink
     * @param string $method
     * @return string
     * @throws GuzzleException
     */
    public function getLink(string $apiLink, string $method = 'GET'): string
    {
        $getLinkUrl = self::ROOT_URL . $apiLink;

        $res = $this->client->request($method, $getLinkUrl, [
            'headers' => [
                'Authorization' => 'OAuth '. $this->token,
            ],
            'query' => [
                'path' => self::REMOTE_DIR .  $this->fileName,
            ]
        ]);

        $data = json_decode($res->getBody()->getContents(), true);

        return $data['href'];
    }
}