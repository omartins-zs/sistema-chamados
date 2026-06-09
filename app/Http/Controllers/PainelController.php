<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class PainelController extends Controller
{
    public function redirecionar(): RedirectResponse
    {
        return redirect('/admin');
    }
}
