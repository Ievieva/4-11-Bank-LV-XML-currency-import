<?php

require_once 'vendor/autoload.php';

use App\Services\CurrencyService;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;

session_start();

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

function database(): Connection
{
    $connectionParams = [
        'dbname' => $_ENV['DB_DATABASE'],
        'user' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD'],
        'host' => $_ENV['DB_HOST'],
        'driver' => 'pdo_mysql',
    ];

    $connection = DriverManager::getConnection($connectionParams);
    $connection->connect();

    return $connection;
}

function query(): QueryBuilder
{
    return database()->createQueryBuilder();
}

$xml = file_get_contents('https://www.bank.lv/vk/ecb.xml');
$service = new Sabre\Xml\Service();
$service->elementMap = [
    '{http://www.bank.lv/vk/LBCurrencyRates.xsd}Currency' => function (Sabre\Xml\Reader $reader) {
        return Sabre\Xml\Deserializer\keyValue($reader, 'http://www.bank.lv/vk/LBCurrencyRates.xsd');
    },
    '{http://www.bank.lv/vk/LBCurrencyRates.xsd}Currencies' => function (Sabre\Xml\Reader $reader) {
        return Sabre\Xml\Deserializer\repeatingElements($reader, '{http://www.bank.lv/vk/LBCurrencyRates.xsd}Currency');
    },
];

$result = ($service->parse($xml))[1]['value'];

$currencyRates = (new CurrencyService())->getData($result);

foreach ($currencyRates as $currency) : ?>

    <p>
        <span style="display: inline-block; width: 100px;"><strong><?php echo $currency->id(); ?></strong></span>
        <span style="display: inline-block; width: 100px;"><?php echo $currency->rate(); ?></span>
    </p>

<?php endforeach; ?>

