<?php
header('Content-Type: text/plain; charset=utf-8');

$breeds = array(
    "Labrador Retriever",
    "German Shepherd",
    "Golden Retriever",
    "French Bulldog",
    "Bulldog",
    "Beagle"
);

$input = isset($_GET['breed']) ? trim($_GET['breed']) : '';

if ($input === '') {
    echo 'Start typing a dog breed...';
    exit;
}

$found = false;
foreach ($breeds as $breed) {
    if (stripos($breed, $input) !== false) {
        if (strcasecmp($breed, $input) === 0) {
            echo "FOUND: $breed";
            $found = true;
            break;
        }
    }
}

if (!$found) {
    echo "Dog breed cannot be found.";
}
