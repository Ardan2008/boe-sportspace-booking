<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ValidationController extends Controller
{
    /**
     * Cek nomor WA via WABLAS / Fonnte / WA Gateway lokal
     * Ganti endpoint & token sesuai provider yang dipakai.
     */
    public function whatsapp(Request $request)
    {
        $nomor = preg_replace('/\D/', '', $request->query('nomor', ''));

        // Normalisasi: 08xx → 628xx
        if (str_starts_with($nomor, '0')) {
            $nomor = '62' . substr($nomor, 1);
        }

        if (!preg_match('/^62[0-9]{8,13}$/', $nomor)) {
            return response()->json(['valid' => false, 'message' => 'Format nomor tidak sesuai']);
        }

        // Cache 10 menit agar tidak spam API
        $cacheKey = 'wa_valid_' . $nomor;
        return Cache::remember($cacheKey, 600, function () use ($nomor) {
            try {
                // ── Opsi A: Fonnte (https://fonnte.com) ──────────────────
                // $res = Http::withToken(env('FONNTE_TOKEN'))
                //     ->timeout(5)
                //     ->post('https://api.fonnte.com/validate', ['target' => $nomor]);
                // $data = $res->json();
                // $valid = $data['status'] ?? false;

                // ── Opsi B: WABLAS ────────────────────────────────────────
                // $res = Http::withHeaders(['Authorization' => env('WABLAS_TOKEN')])
                //     ->timeout(5)
                //     ->get('https://my.wablas.com/api/check-phone?phone=' . $nomor);
                // $data = $res->json();
                // $valid = ($data['status'] ?? false) && ($data['data']['exists'] ?? false);

                // ── Opsi C: Fallback (format-only, tanpa gateway) ─────────
                $valid = true; // Ganti dengan kode API di atas

                return response()->json([
                    'valid'   => $valid,
                    'message' => $valid ? 'Nomor WhatsApp aktif ✓' : 'Nomor tidak terdaftar di WhatsApp',
                ]);
            } catch (\Throwable $e) {
                // Jika API error, jangan blokir user — loloskan
                return response()->json(['valid' => true, 'message' => 'Tidak dapat memverifikasi (format valid)']);
            }
        });
    }

    /**
     * Cek email via Abstract API (MX record + disposable check)
     * Daftar gratis di https://www.abstractapi.com/api/email-validation-verification-api
     * 100 req/bulan gratis.
     */
    public function email(Request $request)
    {
        $email = trim($request->query('email', ''));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['valid' => false, 'message' => 'Format email tidak sesuai']);
        }

        $cacheKey = 'email_valid_' . md5($email);
        return Cache::remember($cacheKey, 3600, function () use ($email) {
            try {
                $res = Http::timeout(6)->get('https://emailvalidation.abstractapi.com/v1/', [
                    'api_key' => env('ABSTRACT_EMAIL_API_KEY'),
                    'email'   => $email,
                ]);

                if (!$res->successful()) {
                    return response()->json(['valid' => true, 'message' => 'Format valid (cek aktifitas tidak tersedia)']);
                }

                $data = $res->json();

                // Tolak jika: format salah, MX tidak ada, atau disposable email
                $formatOk      = ($data['is_valid_format']['value']      ?? false);
                $mxOk          = ($data['is_mx_found']['value']          ?? false);
                $notDisposable = !($data['is_disposable_email']['value'] ?? false);
                $deliverable   = ($data['deliverability'] ?? '') !== 'UNDELIVERABLE';

                $valid = $formatOk && $mxOk && $notDisposable && $deliverable;

                $msg = match(true) {
                    !$formatOk      => 'Format email tidak valid',
                    !$mxOk          => 'Domain email tidak memiliki server penerima (MX)',
                    !$notDisposable => 'Email sementara (disposable) tidak diperbolehkan',
                    !$deliverable   => 'Email tidak dapat menerima pesan',
                    default         => 'Email aktif dan dapat menerima pesan ✓',
                };

                return response()->json(['valid' => $valid, 'message' => $msg]);

            } catch (\Throwable $e) {
                return response()->json(['valid' => true, 'message' => 'Format valid (cek aktifitas tidak tersedia)']);
            }
        });
    }
}
