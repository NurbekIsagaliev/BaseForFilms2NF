<?php

include './functions.php';//подключение  файла  где находятся  функции
include './db.php';//подключение файла  бд

$folderPath = 'films/';//адрес где  лежат  файлы

$pdo = getPDO();//создаем  обьект PDO

$filmsData = extractFilmInfo($folderPath);//Массив  фильмов

$productionCountries = [];//Массив  названия страны производителей  пустой

foreach ($filmsData as $film) { //Заполнение  массива   названия страны  производителей
    $productionCountries[] = $film['production_countries'];
}

$uniqueCountries = array_unique($productionCountries);//Уникальные  значения названия страны  производители

// Проверяем, есть ли уже данные в таблице countries
$sql = 'SELECT COUNT(*) FROM `countries`';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$countryCount = $stmt->fetchColumn();

//Вставление  значений  в  таблицу countries
if ($countryCount === false || $countryCount === 0){
$sql = 'INSERT INTO `countries` (`country_names`) VALUES (:country_names)';
$stmt = $pdo->prepare($sql);

foreach ($uniqueCountries as $country) {
    $stmt->bindParam(':country_names', $country);
    $stmt->execute();
}
}

// Проверяем, есть ли уже данные в таблице films
$sql = 'SELECT COUNT(*) FROM `films`';
$stmt = $pdo->prepare($sql);
$stmt->execute();
$filmCount = $stmt->fetchColumn();

//Вставление  значений  в  таблицу 
if ($filmCount === false || $filmCount === 0) {
$sql = 'INSERT INTO `films` (`name`, `production_countries`) 
        SELECT :name, c.id
        FROM `countries` c
        WHERE c.country_names = :production_countries';

$stmt = $pdo->prepare($sql);

foreach ($filmsData as $film) {
    $stmt->bindParam(':name', $film['name']);
    $stmt->bindParam(':production_countries', $film['production_countries']);
    $stmt->execute();
}
}


$pdo = null;

print_r($filmsData);














