<?php


namespace App\Services;

use App\Models\Book;

class BookService
{
    public static function create(array $data)
    {
        $fileName = data_get($data,'file');
        $file = 'storage/app/public/images/' . $fileName;

        Book::create([
            'name'       => data_get($data, 'name'),
            'user_id'    => data_get($data, 'user_id'),
            'year'       => data_get($data, 'year'),
            'desc'       => data_get($data, 'desc'),
            'section_id' => data_get($data, 'section_id'),
            'user_name' => data_get($data, 'user_name'),
            'file'       => $file,
        ]);

        return true;
    }
}
