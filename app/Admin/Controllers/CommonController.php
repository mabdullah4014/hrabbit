<?php

namespace App\Admin\Controllers;
use App\State;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\DB;

class CommonController extends Controller
{
    use ModelForm;

    public function province()
    {
        $q = $_GET['q'];

        return State::where('country_id', $q)->get(['id', DB::raw('state_name')]);

        return $data;
    }
}