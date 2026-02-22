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
        // Support both oobCode and oobcode, and mode/apiKey
        $mode = $request->query('mode') ?? $request->query('mode');
        $oobCode = $request->query('oobCode') ?? $request->query('oobcode');
        $apiKey = $request->query('apiKey') ?? $request->query('apikey');
        $continueUrl = $request->query('continueUrl') ?? $request->query('continueurl');
        $lang = $request->query('lang', 'en');

        // We will pass the validity status to the view instead of redirecting
        // so the user can see their branded design even if they visit the link directly.
        $isValid = ($mode === 'resetPassword' && !empty($oobCode));

        return view('auth.reset-password', [
            'isValid' => $isValid,
            'oobCode' => $oobCode,
            'apiKey' => $apiKey,
            'continueUrl' => $continueUrl,
            'lang' => $lang,
            'firebaseConfig' => [
                'apiKey' => $apiKey ?? 'AIzaSyDinzQ9rUpwxd4SE0xp3Qgu_GZEwroBT7Y',
                'authDomain' => 'watered-c14bb.firebaseapp.com',
                'projectId' => 'watered-c14bb',
                'storageBucket' => 'watered-c14bb.firebasestorage.app',
                'messagingSenderId' => '47365868791',
                'appId' => '1:47365868791:android:8f6d302003ea14163517f2'
            ]
        ]);
    }
}
