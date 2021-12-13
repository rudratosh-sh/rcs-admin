<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use Response;
use Redirect;
use App\TemplateAdvance;
use Auth;

class TemplateController extends Controller
{
    public function fetchTemplate(Request $request)
    {
        $data['template'] = TemplateAdvance::where('id', $request->id)->where('user_id', Auth::user()->id)->first();
        return response()->json($data);
    }

    public function deleteTemplate(Request $request)
    {
        if (!TemplateAdvance::where('id', $request->id)->where('user_id', Auth::user()->id)->delete())
            return response()->json(array('status' => 'failed', 'message' => 'Something went wrong'));
        else
            return response()->json(array('status' => 'success', 'message' => 'Template Deleted'));
    }
}
