<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ValidationController extends Controller
{
    private function baileysPort()
    {
        return env('BAILEYS_PORT', 3001);
    }

    private function baileysBase()
    {
        return 'http://localhost:' . $this->baileysPort();
    }

    /**
     * Cek nomor WA via Baileys Node.js service (WhatsApp Web API).
     * Jalankan: npm start --prefix baileys-server
     */
    public function whatsapp(Request $request)
    {
        $nomor = preg_replace('/\D/', '', $request->query('nomor', ''));

        if (str_starts_with($nomor, '0')) {
            $nomor = '62' . substr($nomor, 1);
        }

        if (!preg_match('/^62[0-9]{8,13}$/', $nomor)) {
            return response()->json(['valid' => false, 'message' => 'Format nomor tidak sesuai']);
        }

        $base = $this->baileysBase();
        $cacheKey = 'wa_valid_' . $nomor;
        return Cache::remember($cacheKey, 600, function () use ($nomor, $base) {
            try {
                $res = Http::timeout(5)->get($base . '/check', [
                    'nomor' => $nomor,
                ]);

                if (!$res->successful()) {
                    return response()->json(['valid' => true, 'message' => 'Tidak dapat memverifikasi (format valid)']);
                }

                $data = $res->json();

                // Jika Baileys tidak terhubung (blum scan QR), jangan blokir user
                if (($data['status'] ?? '') !== 'connected') {
                    return response()->json(['valid' => true, 'message' => 'Format valid (cek aktifitas tidak tersedia)']);
                }

                return response()->json([
                    'valid'   => $data['valid'] ?? false,
                    'message' => $data['message'] ?? 'Tidak terverifikasi',
                ]);
            } catch (\Throwable $e) {
                return response()->json(['valid' => true, 'message' => 'Tidak dapat memverifikasi (format valid)']);
            }
        });
    }

    /**
     * Cek email aktif via PHP native:
     * 1. filter_var — format
     * 2. checkdnsrr — MX record domain
     * 3. Opsional: cek disposable domain via list sederhana
     */
    public function email(Request $request)
    {
        $email = trim($request->query('email', ''));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['valid' => false, 'message' => 'Format email tidak sesuai']);
        }

        $domain = substr(strrchr($email, '@'), 1);

        $cacheKey = 'email_valid_' . md5($email);
        return Cache::remember($cacheKey, 3600, function () use ($email, $domain) {
            try {
                $mxOk = checkdnsrr($domain, 'MX');

                $disposableDomains = [
                    'mailinator.com', 'guerrillamail.com', 'tempmail.com', 'throwaway.email',
                    'yopmail.com', '10minutemail.com', 'trashmail.com', 'sharklasers.com',
                    'mailnesia.com', 'fakeinbox.com', 'temp-mail.org', 'dispostable.com',
                    'getairmail.com', 'maildrop.cc', 'mailexpire.com', 'spambox.us',
                ];
                $isDisposable = in_array(strtolower($domain), $disposableDomains, true);

                if (!$mxOk) {
                    return response()->json([
                        'valid' => false,
                        'message' => 'Domain email tidak memiliki server penerima (MX)',
                    ]);
                }

                if ($isDisposable) {
                    return response()->json([
                        'valid' => false,
                        'message' => 'Email sementara (disposable) tidak diperbolehkan',
                    ]);
                }

                return response()->json([
                    'valid' => true,
                    'message' => 'Email aktif dan dapat menerima pesan ✓',
                ]);
            } catch (\Throwable $e) {
                return response()->json(['valid' => true, 'message' => 'Format valid (cek aktifitas tidak tersedia)']);
            }
        });
    }
}
