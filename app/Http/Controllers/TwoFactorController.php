<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SecurityAlertMail; // Assuming we have or will create this

class TwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Start the setup for Authenticator App 2FA.
     */
    public function setupApp(Request $request)
    {
        $user = $request->user();
        
        if (!$user->two_factor_secret) {
            $user->two_factor_secret = $this->google2fa->generateSecretKey();
            $user->save();
        }

        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            'INTERNATIONAL AGRO MARKETPLACE (IAM)',
            $user->email,
            $user->two_factor_secret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($qrCodeUrl);

        return response()->json([
            'secret' => $user->two_factor_secret,
            'qr_code_svg' => $qrCodeSvg,
        ]);
    }

    /**
     * Confirm the Authenticator App 2FA.
     */
    public function confirmApp(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = $request->user();

        if ($this->google2fa->verifyKey($user->two_factor_secret, $request->code)) {
            $user->two_factor_confirmed_at = now();
            $user->two_factor_type = 'app';
            
            if (!$user->two_factor_recovery_codes) {
                $user->two_factor_recovery_codes = $this->generateRecoveryCodes();
            }
            
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Authenticator app verified and 2FA enabled.',
                'recovery_codes' => json_decode($user->two_factor_recovery_codes),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'The code you entered is invalid.',
        ], 422);
    }

    /**
     * Enable Email-based 2FA.
     */
    public function enableEmail(Request $request)
    {
        $user = $request->user();
        $user->two_factor_type = 'email';
        $user->two_factor_confirmed_at = now();
        
        if (!$user->two_factor_recovery_codes) {
            $user->two_factor_recovery_codes = $this->generateRecoveryCodes();
        }
        
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Email-based 2FA enabled.',
            'recovery_codes' => json_decode($user->two_factor_recovery_codes),
        ]);
    }

    /**
     * Disable 2FA.
     */
    public function disable(Request $request)
    {
        $user = $request->user();
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->two_factor_type = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => '2FA has been disabled.',
        ]);
    }

    /**
     * Get the user's recovery codes.
     */
    public function getRecoveryCodes(Request $request)
    {
        $user = $request->user();
        if (!$user->two_factor_recovery_codes) {
            return response()->json(['recovery_codes' => []]);
        }

        return response()->json([
            'recovery_codes' => json_decode($user->two_factor_recovery_codes),
        ]);
    }

    /**
     * Regenerate Recovery Codes.
     */
    public function regenerateRecoveryCodes(Request $request)
    {
        $user = $request->user();
        $user->two_factor_recovery_codes = $this->generateRecoveryCodes();
        $user->save();

        return response()->json([
            'recovery_codes' => json_decode($user->two_factor_recovery_codes),
        ]);
    }

    /**
     * Helper to generate discovery/recovery codes.
     */
    protected function generateRecoveryCodes()
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = Str::random(10) . '-' . Str::random(10);
        }
        return json_encode($codes);
    }
}
