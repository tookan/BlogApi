<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class images extends Controller
{
    static function imageCrop($pathway)
    {
        $smallImage = Image::make(storage_path('app/public/' . $pathway));
        $smallImage->crop(300, 900)->resize(100, 300);
        $smallImage->save(storage_path('app/public/small_' . $pathway));
        $bigImage = Image::make(storage_path('app/public/' . $pathway));
        $bigImage->crop(940, 350);
        $bigImage->save(storage_path('app/public/' . $pathway));
    }

    static function avatarCrop($pathway)
    {
        $avatar = Image::make(storage_path('app/public/' . $pathway));
        $avatar->crop(500, 500)->resize(200, 200);
        $avatar->save(storage_path('app/public/' . $pathway));
    }

    static function deleteImages($id)
    {
        Storage::disk('public')->deleteDirectory('photo/n' . $id);
        Storage::disk('public')->deleteDirectory('small_photo/n' . $id);
    }
}
