<?php
require_once './vendor/autoload.php';

use App\ApiClient;
use App\Handler;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apiClient = new ApiClient();
$cityId = $argv[1];
$results = [];
$page = 1;

while (true) {
    $data = json_decode(
        $apiClient->getDiscounts($cityId, $page),
        JSON_UNESCAPED_UNICODE
    );

    if (empty($data['results'])) {
        echo "Parsing finished\n";
        --$page;
        break;
    }

    $totalOnPage = count($data['results']);
    $results = array_merge($results, $data['results']);

    echo "Got $totalOnPage records on $page page\n";

    ++$page;

    sleep(2);
}

$handler = new Handler($apiClient->client);
$handler->updateAndUploadResults($cityId, $results);

