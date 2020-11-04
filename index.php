<?php

require_once 'vendor/autoload.php';

use App\Controllers\CurrencyController;
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

foreach ($result as $currency) {
    CurrencyController::add($currency['ID'], $currency['Rate']);
}

$currencyRates = CurrencyController::index();

foreach ($currencyRates as $currency) : ?>

    <p>
        <strong><?php echo $currency->id(); ?></strong>
        <?php echo $currency->rate(); ?>
    </p>

<?php endforeach; ?>

