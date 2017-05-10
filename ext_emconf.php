<?php

$EM_CONF[$_EXTKEY] = [
    'title'            => 'HTML minifier',
    'description'      => 'Minifies the TYPO3 output in frontend.',
    'category'         => 'fe',
    'constraints'      => [
        'depends'   => [
            'typo3' => '7.6.0-8.7.99',
        ],
        'conflicts' => [],
        'suggests'  => []
    ],
    'state'            => 'stable',
    'uploadfolder'     => false,
    'createDirs'       => '',
    'clearCacheOnLoad' => true,
    'author'           => 'Tim Schreiner',
    'author_email'     => 'schreiner.tim@gmail.com',
    'author_company'   => '',
    'version'          => '1.0.1'
];
