<?php
require_once "vendor/autoload.php";

use LucidFrame\Console\ConsoleTable;

$companyName = readline("Enter company name: ");

$resourceId = "25e80bf3-f107-4ab4-89ef-251b5b9374e9";
$ch = curl_init(
    "https://data.gov.lv/dati/lv/api/3/action/datastore_search?q=$companyName&resource_id=$resourceId"
);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$serverOutput = curl_exec($ch);
curl_close($ch);

$searchResults = json_decode($serverOutput);

$properties = [
    "_id",
    "regcode",
    "sepa",
    "name",
    "name_before_quotes",
    "name_in_quotes",
    "name_after_quotes",
    "without_quotes",
    "regtype",
    "regtype_text",
    "type",
    "type_text",
    "registered",
    "terminated",
    "closed",
    "address",
    "index",
    "addressid",
    "region",
    "city",
    "atvk",
    "reregistration_term",
    "rank",
];

if (!empty($searchResults->result->records)) {
    foreach ($searchResults->result->records as $record) {
        $table = new ConsoleTable();
        $table
            ->addHeader("Property")
            ->addHeader("Value")
            ->addRow()
            ->setPadding(2);
        foreach ($properties as $property) {
            if ($record->$property === null) {
                $table
                    ->addColumn($property)
                    ->addColumn(" ")
                    ->addRow();
            } else {
                $table
                    ->addColumn($property)
                    ->addColumn($record->$property)
                    ->addRow();
            }
        }
        $table->display();
    }
    $companyCount = count($searchResults->result->records);
    echo $companyCount > 1
        ? "\n($companyCount) Companies found.\n"
        : "\n(1) Company found.\n";
} else {
    echo "\nCompany not found.\n";
}
