<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Face Verification Settings
    |--------------------------------------------------------------------------
    |
    | similarity_threshold: minimum cosine similarity (0-1) required to accept
    |                       a candidate embedding against the stored reference.
    | embedding_length:    expected descriptor length. Adjust if you switch
    |                       models with different vector sizes.
    */
    'similarity_threshold' => (float) env('FACE_SIMILARITY_THRESHOLD', 0.58),
    'embedding_length' => (int) env('FACE_EMBEDDING_LENGTH', 128),
];

