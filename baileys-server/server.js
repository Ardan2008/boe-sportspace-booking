import express from 'express';
import { makeWASocket, useMultiFileAuthState, DisconnectReason, fetchLatestBaileysVersion } from '@whiskeysockets/baileys';
import { Boom } from '@hapi/boom';
import pino from 'pino';
import qrcode from 'qrcode-terminal';
import path from 'path';
import fs from 'fs';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const PORT = process.env.BAILEYS_PORT || 3001;
const AUTH_DIR = path.join(__dirname, 'auth_info');

if (!fs.existsSync(AUTH_DIR)) {
    fs.mkdirSync(AUTH_DIR, { recursive: true });
}

const app = express();
let sock = null;
let connectionState = 'disconnected';
let qrCode = null;
let lastError = null;

const logger = pino({ level: 'warn' });

async function startSocket() {
    try {
        const { state, saveCreds } = await useMultiFileAuthState(AUTH_DIR);
        const { version } = await fetchLatestBaileysVersion();

        sock = makeWASocket({
            version,
            auth: state,
            printQRInTerminal: false,
            browser: ['BOE SpaceReserve', 'Chrome', '1.0'],
            syncFullHistory: false,
            markOnlineOnConnect: false,
            logger,
            generateHighQualityLinkPreview: false,
        });

        sock.ev.on('creds.update', saveCreds);

        sock.ev.on('connection.update', (update) => {
            const { connection, lastDisconnect, qr } = update;

            if (qr) {
                qrCode = qr;
                connectionState = 'awaiting_scan';
                lastError = null;
                qrcode.generate(qr, { small: true });
            }

            if (connection === 'open') {
                connectionState = 'connected';
                qrCode = null;
                lastError = null;
                console.log('[Baileys] WhatsApp terhubung!');
            }

            if (connection === 'close') {
                const statusCode = (lastDisconnect?.error instanceof Boom)
                    ? lastDisconnect.error.output?.statusCode
                    : 0;

                const shouldReconnect = statusCode !== DisconnectReason.loggedOut;
                connectionState = statusCode === DisconnectReason.loggedOut ? 'logged_out' : 'disconnected';
                lastError = `Closed: ${statusCode}`;
                console.log(`[Baileys] Koneksi ditutup (${statusCode}). Reconnect: ${shouldReconnect}`);

                if (shouldReconnect) {
                    setTimeout(startSocket, 3000);
                }
            }
        });
    } catch (err) {
        console.error('[Baileys] Gagal start socket:', err.message);
        lastError = err.message;
        setTimeout(startSocket, 5000);
    }
}

app.get('/health', (req, res) => {
    res.json({
        status: connectionState,
        connected: connectionState === 'connected',
        hasQr: !!qrCode,
        lastError,
    });
});

app.get('/qr', (req, res) => {
    if (qrCode) {
        res.json({ qr: qrCode, status: connectionState });
    } else {
        res.json({ qr: null, status: connectionState, message: 'Tidak ada QR saat ini' });
    }
});

app.get('/check', async (req, res) => {
    try {
        const nomor = (req.query.nomor || '').replace(/\D/g, '');

        if (!nomor || !/^62\d{8,13}$/.test(nomor)) {
            return res.json({ valid: false, message: 'Format nomor tidak sesuai' });
        }

        if (connectionState !== 'connected') {
            return res.json({
                valid: false,
                message: connectionState === 'awaiting_scan'
                    ? 'WhatsApp belum dipindai (scan QR di /qr)'
                    : 'WhatsApp tidak terhubung',
                status: connectionState,
            });
        }

        const [result] = await sock.onWhatsApp(nomor);

        if (result && result.exists) {
            res.json({ valid: true, message: 'Nomor WhatsApp aktif ✓' });
        } else {
            res.json({ valid: false, message: 'Nomor tidak terdaftar di WhatsApp' });
        }
    } catch (err) {
        console.error('[Baileys] Check error:', err.message);
        res.json({ valid: false, message: 'Gagal memverifikasi nomor', error: err.message });
    }
});

app.listen(PORT, () => {
    console.log(`[Baileys] Server berjalan di port ${PORT}`);
    startSocket();
});
