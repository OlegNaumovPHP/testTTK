<?php


namespace App\Services;

use App\Models\Section;

class SectionService
{
    public static function create(array $data)
    {
        $fileName = data_get($data,'file');
        $file = 'public/storage/images/' . $fileName;

        Section::create([
            'name'      => data_get($data, 'name'),
            'desc'      => data_get($data, 'desc'),
            'file'      => $file,
        ]);

        return true;
    }
}
