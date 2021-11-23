<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use Response;
use Redirect;
use App\TemplateAdvance;

class TemplateController extends Controller
{
    public function fetchTemplate(Request $request)
    {
        $data['template'] = TemplateAdvance::where('id',$request->id)->first();
        return response()->json($data);
    }
}
