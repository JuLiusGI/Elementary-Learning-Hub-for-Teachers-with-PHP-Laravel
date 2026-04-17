<?php

return [
    'name' => env('SCHOOL_NAME', 'My Elementary School'),
    'lrn_id' => env('SCHOOL_LRN_ID', '000000'),
    'address' => env('SCHOOL_ADDRESS', ''),
    'region' => env('SCHOOL_REGION', ''),

    'grade_levels' => [
        'kinder' => 'Kindergarten',
        'grade_1' => 'Grade 1',
        'grade_2' => 'Grade 2',
        'grade_3' => 'Grade 3',
        'grade_4' => 'Grade 4',
        'grade_5' => 'Grade 5',
        'grade_6' => 'Grade 6',
    ],

    'quarters' => ['Q1', 'Q2', 'Q3', 'Q4'],

    'grading' => [
        'ww_weight' => 0.40,
        'pt_weight' => 0.40,
        'qa_weight' => 0.20,
        'passing_grade' => 75,
    ],

    'kinder_domains' => [
        'socio_emotional' => 'Socio-Emotional Development',
        'language' => 'Language Development',
        'cognitive' => 'Cognitive Development',
        'physical' => 'Physical Development',
        'creative' => 'Creative Expression',
    ],

    'kinder_ratings' => [
        'beginning' => 'Beginning',
        'developing' => 'Developing',
        'proficient' => 'Proficient',
    ],
];
