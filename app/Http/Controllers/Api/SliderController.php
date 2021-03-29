<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slider;

class SliderController extends Controller
{
     /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get data sliders
        $sliders = Slider::latest()->get();

        //return with response JSON
        return response()->json([
            'success' => true,
            'message' => 'List Data Sliders',
            'data'    => $sliders,
        ], 200);
    }
}
