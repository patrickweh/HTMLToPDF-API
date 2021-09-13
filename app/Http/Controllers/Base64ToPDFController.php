<?php

namespace App\Http\Controllers;

use App\Models\File;
use iio\libmergepdf\Merger;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;

class Base64ToPDFController extends Controller
{
    public function convert(Request $request)
    {
        $body = $request->input('file');
        $token = $request->input('token');
        if($token === null){
            $token = md5(Str::random('40'));
        }

        $filename = storage_path('app/public/'. md5(Str::random('40')) . '.pdf');

        Browsershot::html(base64_decode($body))
            ->paperSize(222, 157)
            ->showBackground()
            ->waitUntilNetworkIdle()
            ->savePdf($filename);

        $file = new File();
        $file->path = $filename;
        $file->token = $token;
        $file->save();
        return response()->json(['token' => $token]);
    }

    public function merge(string $token)
    {
        $merger = new Merger;
        $files = File::query()->where('token', "=", $token)->get();
        foreach ($files as $file){
            $merger->addFile($file->path);
        }
        $createdPDF = $merger->merge();
        foreach ($files as $file){
            unlink($file->path);
            $file->delete();
        }
        return response($createdPDF, 200, ['Content-Type' => 'application/pdf']);
    }
}
