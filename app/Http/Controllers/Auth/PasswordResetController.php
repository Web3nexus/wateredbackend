<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PasswordResetController extends Controller
{
    /**
     * Show the custom branded password reset form.
     */
    public function showResetForm(Request $request)
    {
        $mode = $request->query('mode');
        $oobCode = $request->query('oobCode');
        $apiKey = $request->query('apiKey');
        $continueUrl = $request->query('continueUrl');
        $lang = $request->query('lang', 'en');

        // We only care about resetPassword mode
        if ($mode !== 'resetPassword' || !$oobCode) {
            return redirect()->route('home');
        }

        return view('auth.reset-password', [
            'oobCode' => $oobCode,
            'apiKey' => $apiKey,
            'continueUrl' => $continueUrl,
            'lang' => $lang,
            'firebaseConfig' => [
                'apiKey' => 'AIzaSyDinzQ9rUpwxd4SE0xp3Qgu_GZEwroBT7Y',
                'authDomain' => 'watered-c14bb.firebaseapp.com',
                'projectId' => 'watered-c14bb',
                'storageBucket' => 'watered-c14bb.firebasestorage.app',
                'messagingSenderId' => '47365868791',
                'appId' => '1:47365868791:android:8f6d302003ea14163517f2'
            ]
        ]);
    }
}
