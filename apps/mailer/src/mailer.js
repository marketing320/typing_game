import nodemailer from 'nodemailer';
import { otpTemplate } from './templates/otp.js';

const transporter = nodemailer.createTransport({
    host: process.env.SMTP_HOST,
    port: parseInt(process.env.SMTP_PORT || '587'),
    secure: process.env.SMTP_PORT === '465',
    auth: {
        user: process.env.SMTP_USER,
        pass: process.env.SMTP_PASS,
    },
});

export async function sendOtpEmail({ email, otp, username }) {
    const html = otpTemplate({ otp, username });

    await transporter.sendMail({
        from: `"${process.env.SMTP_FROM_NAME || 'Typing Monkey'}" <${process.env.SMTP_FROM_EMAIL}>`,
        to: email,
        subject: `Your Typing Monkey OTP: ${otp}`,
        html,
    });
}
