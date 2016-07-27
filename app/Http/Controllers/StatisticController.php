<?php

namespace App\Http\Controllers;

use App\Components\Statistic\Storage;
use Illuminate\Http\Request;

use App\Http\Requests;

class StatisticController extends Controller
{
    public function index()
    {
        /** @var Storage $storage */
        $storage = app(Storage::class);

        return view('statistic', [
            'total' => $storage->getTotal(),
            'perPage' => $storage->getPerPage()
        ]);
    }
}
