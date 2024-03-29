<?php

namespace App\Http\Controllers\Auth;

use Core\Controller;
use Core\Formity;
use Core\Request;
use App\Auth;

class LoginController extends Controller
{
    function __construct()
    {
        $this->library('Formity');
    }

    function logout(Request $request)
    {
        Auth::close();
    }
    function login()
    {
        $form = Formity::instance('login');
        $form->setUniqueId('login');
        $form->addField('usuario', 'input:text')->setIcon('user');
        $form->addField('clave', 'input:password')->setIcon('lock');

        $error = null;
        if ($form->byRequest()) {
            if ($form->isValid($error)) {
                $data = $form->getData();
                $rp = Auth::check($data['usuario'], $data['clave'], $error);
                if (!empty($rp)) {
                    return response()->json([
                        'status' => 'success',
                        'redirect' => '/?ingresando'
                    ]);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'error' => $error
                    ]);
                }
            }
        }else{
            return view('login.login', compact('form', 'error'));
        }
    }
}
