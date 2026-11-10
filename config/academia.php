<?php

return [

    /*
    | Arquivo JSON onde ficam os professores cadastrados.
    */
    'professores_arquivo' => env('PROFESSORES_ARQUIVO', database_path('data/professores.json')),

    /*
    | Pasta (dentro de public/) onde ficam as fotos dos professores.
    */
    'fotos_url_base' => 'img/professores',
    'fotos_diretorio' => public_path('img/professores'),
    'foto_padrao' => 'img/professores/padrao.svg',

    /*
    | Modalidades que um professor pode ministrar.
    */
    'modalidades' => ['Musculação', 'Pilates', 'Funcional', 'Spinning'],

];
