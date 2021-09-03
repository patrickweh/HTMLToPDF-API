<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;

class Base64ToPDFController extends Controller
{
    public function convert(Request $request)
    {
        $body = $request->input('file');

        return Browsershot::html(base64_decode($body))
            ->paperSize(222, 157)
            ->showBackground()
            ->waitUntilNetworkIdle()
            ->pdf();
    }
}
