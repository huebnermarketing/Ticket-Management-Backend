<?php

use Illuminate\Support\Str;

if (!function_exists('uploadImage')) {
    function uploadImage($request, $imagekey, $filePath, $data)
    {
        $s3 = Storage::disk('s3');
        $uploadedPhoto = null;
        $profilePhoto = $request->file($imagekey);
        if (!empty($profilePhoto)) {
            if (File::size($profilePhoto) > 2097152) {
                return RestResponse::warning('Profile Image upto 2 Mb max.', 422);
            }
            $ext = $profilePhoto->getClientOriginalExtension();
            if (!in_array(strtolower($ext), array("png", "jpeg", "jpg", "gif", "svg"))) {
                return RestResponse::warning('Profile Image must be a PNG, JPEG, GIF, SVG file.', 422);
            }

            $imageName = time() . '-' . rand(0, 100) . '.' . $profilePhoto->getClientOriginalExtension();
            $s3->put($filePath . '/' . $imageName, file_get_contents($profilePhoto), 'public');
            if ($data[$imagekey] != "") {
                $s3->delete($filePath . '/' . $data[$imagekey]);
            }
            $uploadedPhoto = $imageName;
        } else {
            if (!Str::contains($request[$imagekey], $data->getAttributes()[$imagekey])) {
                return RestResponse::warning('Whoops something went wrong.');
            }
        }
        return $uploadedPhoto;
    }
}
?>
