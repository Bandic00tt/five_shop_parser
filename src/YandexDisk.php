<?php
namespace App;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class YandexDisk
{
    private const GET_LINK_METHOD = 'GET';
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
     * @return string
     * @throws GuzzleException
     */
    public function getLink(string $apiLink): string
    {
        $getLinkUrl = self::ROOT_URL . $apiLink;

        $res = $this->client->request(self::GET_LINK_METHOD, $getLinkUrl, [
            'headers' => [
                'Authorization' => 'OAuth '. $this->token,
            ],
            'query' => [
                'path' => self::REMOTE_DIR .  $this->fileName,
                'overwrite' => true,
            ]
        ]);

        $data = json_decode($res->getBody()->getContents(), true);

        return $data['href'];
    }
}