<?php

$composerVersions = explode(PHP_EOL, shell_exec("docker run --rm -i webcoast_docker_images_builder sh -c 'get-composer-versions.sh|filter-versions.php \">= 1.8.0\"'"));

$phpVersionConstraints = [
    '1.8' => [
        'max' => '7.3'
    ]
];

foreach ($composerVersions as $composerVersion) {
    foreach ($phpVersions as $phpVersion) {
        foreach ($distributions as $distribution => $distConfig) {
            if (version_compare($phpVersion, $distConfig['minPhpVersion'], '>=') && version_compare($phpVersion, $distConfig['maxPhpVersion'], '<=')) {
                if (preg_match('/^(\d+\.\d+)/', $composerVersion, $composerMajorVersion)) {
                    $versionCombinationAllowed = true;
                    if (isset($phpVersionConstraints[$composerMajorVersion[1]])) {
                        if (isset($phpVersionConstraints[$composerMajorVersion[1]]['min'])) {
                            if (!version_compare($phpVersion, $phpVersionConstraints[$composerMajorVersion[1]]['min'], '>=')) {
                                $versionCombinationAllowed = false;
                            }
                        }
                        if (isset($phpVersionConstraints[$composerMajorVersion[1]]['max'])) {
                            if (!version_compare($phpVersion, $phpVersionConstraints[$composerMajorVersion[1]]['max'], '<=')) {
                                $versionCombinationAllowed = false;
                            }
                        }
                    }
                    if ($versionCombinationAllowed) {
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
    }
}
