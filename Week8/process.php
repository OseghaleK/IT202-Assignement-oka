<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Restaurant Array Results</title>
</head>
<body>

<?php

$restaurants = [
    $_POST['r1'],
    $_POST['r2'],
    $_POST['r3'],
    $_POST['r4'],
    $_POST['r5'],
    $_POST['r6'],
    $_POST['r7'],
    $_POST['r8']
];

function printWithoutKeys($arr) {
    foreach ($arr as $val) {
        echo $val . "<br>";
    }
}

function printWithKeys($arr) {
    foreach ($arr as $key => $val) {
        echo $key . " => " . $val . "<br>";
    }
}

echo "<h3>Original Array (Without Keys)</h3>";
printWithoutKeys($restaurants);

echo "<h3>Original Array (With Keys)</h3>";
printWithKeys($restaurants);

unset($restaurants[count($restaurants)-1]);
unset($restaurants[count($restaurants)-2]);

echo "<h3>After Deleting Last 2 (Without Keys)</h3>";
printWithoutKeys($restaurants);

echo "<h3>After Deleting Last 2 (With Keys)</h3>";
printWithKeys($restaurants);

$restaurants_no_gaps = array_values($restaurants);

echo "<h3>After Removing Gaps (Without Keys)</h3>";
printWithoutKeys($restaurants_no_gaps);

echo "<h3>After Removing Gaps (With Keys)</h3>";
printWithKeys($restaurants_no_gaps);

$ascending = $restaurants_no_gaps;
sort($ascending);

echo "<h3>Sorted Ascending (Without Keys)</h3>";
printWithoutKeys($ascending);

echo "<h3>Sorted Ascending (With Keys)</h3>";
printWithKeys($ascending);

$descending = $restaurants_no_gaps;
rsort($descending);

echo "<h3>Sorted Descending (Without Keys)</h3>";
printWithoutKeys($descending);

echo "<h3>Sorted Descending (With Keys)</h3>";
printWithKeys($descending);

$keep_keys_value = $restaurants_no_gaps;
asort($keep_keys_value);

echo "<h3>Ascending by Value (Keep Keys) Without Keys</h3>";
printWithoutKeys($keep_keys_value);

echo "<h3>Ascending by Value (Keep Keys) With Keys</h3>";
printWithKeys($keep_keys_value);

$by_keys = $restaurants_no_gaps;
ksort($by_keys);

echo "<h3>Ascending by Keys (Without Keys)</h3>";
printWithoutKeys($by_keys);

echo "<h3>Ascending by Keys (With Keys)</h3>";
printWithKeys($by_keys);

?>
</body>
</html>
