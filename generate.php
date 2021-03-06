#!/usr/bin/env php
<?php

$baseDir = dirname(__FILE__);

$dockerNoCache = in_array('--no-cache', $argv) ? ' --no-cache' : '';
$dockerPull = in_array('--pull', $argv) ? ' --pull' : '';

passthru("docker build${dockerPull}${dockerNoCache} ${baseDir}/docker/builder -t webcoast_docker_images_builder");

$phpVersions = ['5.6', '7.0', '7.1', '7.2', '7.3', '7.4'];
$distributions = [
    'stretch' => [
        'minPhpVersion' => '5.6',
        'maxPhpVersion' => '7.3'
    ],
    'buster' => [
        'minPhpVersion' => '7.1',
        'maxPhpVersion' => '7.4'
    ],
    'alpine' => [
        'minPhpVersion' => '5.6',
        'maxPhpVersion' => '7.0'
    ],
    'alpine3.10' => [
        'minPhpVersion' => '7.1',
        'maxPhpVersion' => '7.4'
    ]
];

$ignoredDirectories = [
    'docker',
    'smtp-relay',
    'nginx-php',
    'typo3-cms',
    'mysql'
];

$productsToBuild = [];

foreach (array_diff(scandir($baseDir), ['.', '..']) as $file) {
    if (is_dir($baseDir . DIRECTORY_SEPARATOR . $file) && strpos($file, '.') !== 0 && !in_array($file, $ignoredDirectories)) {
        $productsToBuild[] = $baseDir . DIRECTORY_SEPARATOR . $file;
    }
}

$buildMatrix = [];
foreach ($productsToBuild as $productDirectory) {
    if (file_exists($productDirectory . DIRECTORY_SEPARATOR . 'generate_versions.php')) {
        $versionsToBuild = [];

        require_once $productDirectory . DIRECTORY_SEPARATOR . 'generate_versions.php';

        foreach ($versionsToBuild as $tag => $config) {
            if (!empty($tag)) {
                $buildMatrix[] = basename($productDirectory) . DIRECTORY_SEPARATOR . $tag;
                // Remove existing directory to clean up old files
                if (file_exists($productDirectory . DIRECTORY_SEPARATOR . $tag)) {
                    passthru('rm -r ' . $productDirectory . DIRECTORY_SEPARATOR . $tag);
                }
                // Create new directory
                if (!mkdir($productDirectory . DIRECTORY_SEPARATOR . $tag, 0755, true)) {
                    echo 'Directory for tag "' . $tag . '" could not be created. Exiting.';
                    exit(1);
                }
                $dockerFileTemplate = file_get_contents($productDirectory . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'Dockerfile');
                if (file_exists($productDirectory . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'README.md')) {
                    $readMeTemplate = file_get_contents($productDirectory . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'README.md');
                } else {
                    $readMeTemplate = '';
                }
                foreach ($config as $variable => $value) {
                    $dockerFileTemplate = str_replace('<!-- VAR ' . $variable . ' -->', $value, $dockerFileTemplate);
                    $readMeTemplate = str_replace('<!-- VAR ' . $variable . ' -->', $value, $readMeTemplate);
                }
                preg_match_all('/<!-- INSERT (\S+) -->/', $dockerFileTemplate, $insertMatches, PREG_SET_ORDER);
                foreach ($insertMatches as $match) {
                    $insertContent = '';
                    if (file_exists($productDirectory . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . $match[1] . '.' . $config['distribution'])) {
                        $insertContent = file_get_contents($productDirectory . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . $match[1] . '.' . $config['distribution']);
                    }
                    $dockerFileTemplate = str_replace($match[0] . PHP_EOL, $insertContent, $dockerFileTemplate);
                }
                file_put_contents($productDirectory . DIRECTORY_SEPARATOR . $tag . DIRECTORY_SEPARATOR . 'Dockerfile', $dockerFileTemplate);
                if (!empty($readMeTemplate)) {
                    file_put_contents($productDirectory . DIRECTORY_SEPARATOR . $tag . DIRECTORY_SEPARATOR . 'README.md', $readMeTemplate);
                }
                if (file_exists($productDirectory . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . '.to_copy')) {
                    foreach (file($productDirectory . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . '.to_copy') as $copyDir) {
                        $copyDir = trim($copyDir);
                        passthru('cp -r ' . $productDirectory . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . $copyDir . DIRECTORY_SEPARATOR . ' ' . $productDirectory . DIRECTORY_SEPARATOR . $tag . DIRECTORY_SEPARATOR . $copyDir . DIRECTORY_SEPARATOR);
                    }
                }
            }
        }
    }
}

$workflowFile = $baseDir . DIRECTORY_SEPARATOR . '.github' . DIRECTORY_SEPARATOR . 'workflows' . DIRECTORY_SEPARATOR . 'build.yml';
$workflowContent = file_get_contents($workflowFile);
preg_match('/(# IMAGE_LIST_START).*?([ ]+)(# IMAGE_LIST_END)/s', $workflowContent, $matches);
$workflowContent = preg_replace('/(# IMAGE_LIST_START).*?([ ]+)(# IMAGE_LIST_END)/s', $matches[1] . "\n${matches[2]}'" . implode("',\n" . $matches[2] . "'", $buildMatrix) . "'\n${matches[2]}${matches[3]}", $workflowContent);
file_put_contents($workflowFile, $workflowContent);
