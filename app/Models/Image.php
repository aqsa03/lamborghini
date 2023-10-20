<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Create Image object from form request and store file
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string   $input_name
     * @param  string   $base_path
     * @return \Illuminate\Http\Response
     */
    public static function createAndStoreFromRequest($request, $input_name, $base_path)
    {
        if ($request->has($input_name) and $request->file($input_name)->isValid()) {
            $uploadedFile = $request->file($input_name);
            $imageLocalPath = $uploadedFile->store($base_path, 'public');
            $imagePath = $uploadedFile->store($base_path, 'ftp');
            $imageLocalPathSplitted = explode("/", $imageLocalPath);
            return Image::create([
                'name' => $uploadedFile->getClientOriginalName(),
                'filename' => $uploadedFile->getClientOriginalName(),
                'mime_type' => $uploadedFile->getMimeType(),
                'local_path' => $imageLocalPath,
                'upload_filename' => $imageLocalPathSplitted[count($imageLocalPathSplitted) - 1],
                'url' => config('filesystems.disks.ftp.public_base_url').$imagePath
            ]);
        }
        return false;
    }

}
