<?php

$composerVersions = explode(PHP_EOL, shell_exec("docker run --rm -i webcoast_docker_images_builder sh -c 'get-composer-versions.sh|filter-versions.php \">= 1.8.0\"'"));

foreach ($composerVersions as $composerVersion) {
    foreach ($phpVersions as $phpVersion) {
        foreach ($distributions as $distribution => $distConfig) {
            if (version_compare($phpVersion, $distConfig['minPhpVersion'], '>=') && version_compare($phpVersion, $distConfig['maxPhpVersion'], '<=')) {
                preg_match('/^(\d+\.\d+)/', $composerVersion, $composerMajorVersion);
                $versionsToBuild["${composerMajorVersion[1]}-php${phpVersion}-${distribution}"] = [
                    'tag' => "${composerMajorVersion[1]}-php${phpVersion}-${distribution}",
                    'composerVersion' => $composerVersion,
                    'phpVersion' => $phpVersion,
                    'distribution' => $distribution
                ];
            }
        }
    }
}
