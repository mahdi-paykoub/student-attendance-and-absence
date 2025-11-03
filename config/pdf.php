<?php

return [
   'mode'                  => 'utf-8',
   'format'                => 'A4',
   'author'                => 'Farid',
   'subject'               => '',
   'keywords'              => '',
   'creator'               => 'Farid',
   'display_mode'          => 'fullpage',
   'tempDir'               => storage_path('app/mpdf_temp'),
   'font_path' => base_path('public/assets/IRANSans/'),
   'font_data' => [
      'fa' => [
         'R'  => 'IRANSans.ttf',
        
         'useOTL' => 0xFF,
         'useKashida' => 75,
      ],
      'en' => [
         'R'  => 'Gothic/Century Gothic.ttf',
         'B'  => 'Gothic/GOTHICB.ttf',
      ]
   ]
];