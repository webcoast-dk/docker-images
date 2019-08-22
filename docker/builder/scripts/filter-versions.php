#!/usr/bin/env php
<?php

if ($argc === 0) {
    echo 'Missing version constraints';
    exit(1);
} else {
    $constraints = [];
    foreach ($argv as $versionConstraint) {
        $constraints[] = explode(' ', $versionConstraint);
    }
}

$versions = '';
while ($input = fread(STDIN, 1000)) {
    $versions .= $input;
}

$versions = preg_split('/\s/', $versions);

$finalVersions = [];
foreach ($versions as $version) {
    foreach ($constraints as $constraint) {
        if (version_compare($version, $constraint[1], $constraint[0])) {
            if (preg_match('/^(\d+\.\d+)\.\d+/', $version, $matches)) {
                if (!isset($finalVersions[$matches[1]])) {
                    $finalVersions[$matches[1]] = $version;
                }
            }

        }
    }
}

echo implode(PHP_EOL, $finalVersions);
