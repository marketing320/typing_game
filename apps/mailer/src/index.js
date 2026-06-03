import 'dotenv/config';
import express from 'express';
import { sendOtpEmail } from './mailer.js';

const app = express();
app.use(express.json());

const PORT = process.env.MAILER_PORT || 4001;

app.post('/send-otp', async (req, res) => {
    const { email, otp, username } = req.body;

    if (!email || !otp) {
        return res.status(422).json({ success: false, message: 'email and otp are required' });
    }

    try {
        await sendOtpEmail({ email, otp, username: username || 'Player' });
        return res.json({ success: true, message: 'OTP sent' });
    } catch (err) {
        console.error('Mail error:', err.message);
        return res.status(500).json({ success: false, message: 'Failed to send email' });
    }
});

app.get('/health', (req, res) => res.json({ ok: true }));

app.listen(PORT, () => {
    console.log(`Mailer service running on port ${PORT}`);
});
