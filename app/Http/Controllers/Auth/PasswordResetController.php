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
        $mode = $request->input('mode', $request->input('mode', 'resetPassword'));
        $oobCode = $request->input('oobCode') ?? $request->input('oobcode');
        $apiKey = $request->input('apiKey') ?? $request->input('apikey');
        $continueUrl = $request->input('continueUrl') ?? $request->input('continueurl');
        $lang = $request->input('lang', 'en');

        // Log for debugging to see why it's failing
        \Illuminate\Support\Facades\Log::info('Password Reset Page Accessed', [
            'mode' => $mode,
            'oobCode_present' => !empty($oobCode),
            'apiKey_present' => !empty($apiKey),
            'all_params' => $request->all(),
        ]);

        // Be more lenient - if oobCode is present, let the Firebase JS try to verify it
        $isValid = !empty($oobCode);

        return view('auth.reset-password', [
            'isValid' => $isValid,
            'mode' => $mode,
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
