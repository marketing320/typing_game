export function otpTemplate({ otp, username }) {
    return `
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Typing Monkey OTP</title>
</head>
<body style="font-family: Arial, sans-serif; background: #fef3c7; margin: 0; padding: 20px;">
  <div style="max-width: 480px; margin: 0 auto; background: #fff; border-radius: 16px; padding: 32px; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">
    <div style="text-align: center; margin-bottom: 24px;">
      <span style="font-size: 48px;">🐒</span>
      <h1 style="color: #92400e; margin: 8px 0 0;">Typing Monkey</h1>
    </div>
    <p style="color: #374151; font-size: 16px;">Hi <strong>${username}</strong>,</p>
    <p style="color: #374151; font-size: 16px;">Your OTP code for the challenge is:</p>
    <div style="text-align: center; margin: 24px 0;">
      <span style="display: inline-block; background: #fef3c7; border: 2px dashed #d97706; border-radius: 12px; padding: 16px 32px; font-size: 36px; font-weight: bold; letter-spacing: 12px; color: #92400e;">${otp}</span>
    </div>
    <p style="color: #6b7280; font-size: 14px;">This code expires in 10 minutes. Do not share it with anyone.</p>
    <p style="color: #6b7280; font-size: 14px;">Good luck on the challenge! 🍌</p>
  </div>
</body>
</html>
`;
}
