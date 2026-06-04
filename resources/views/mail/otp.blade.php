<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your OTP Code</title>
</head>
<body style="margin:0;padding:0;font-family:Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#fef3c7;padding:32px 16px;">
    <tr>
      <td align="center">
        <table width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;">

          <!-- Card -->
          <tr>
            <td style="background:#ffffff;border-radius:16px;padding:36px 32px;box-shadow:0 4px 24px rgba(0,0,0,0.08);">

              <!-- Header -->
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td align="center" style="padding-bottom:24px;">
                    <h1 style="margin:10px 0 0;color:#92400e;font-size:22px;font-weight:700;">
                      {{ config('app.name') }}
                    </h1>
                  </td>
                </tr>
              </table>

              <!-- Body -->
              <p style="color:#374151;font-size:15px;margin:0 0 8px;">
                Hi <strong>{{ $username }}</strong>,
              </p>
              <p style="color:#374151;font-size:15px;margin:0 0 24px;">
                Your one-time password for the challenge is:
              </p>

              <!-- OTP box -->
              <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                <tr>
                  <td align="center">
                    <div style="display:inline-block;background:#fef3c7;border:2px dashed #d97706;border-radius:12px;padding:16px 32px;">
                      <span style="font-size:36px;font-weight:700;letter-spacing:12px;color:#92400e;font-family:monospace;">
                        {{ $otp }}
                      </span>
                    </div>
                  </td>
                </tr>
              </table>

              <!-- Footer notes -->
              <p style="color:#6b7280;font-size:13px;margin:0 0 6px;">
                This code expires in <strong>{{ config('app.otp_expiry_minutes', 10) }} minutes</strong>.
                Do not share it with anyone.
              </p>
              <p style="color:#6b7280;font-size:13px;margin:0;">
                Good luck on the challenge!
              </p>

            </td>
          </tr>

          <!-- Sub-footer -->
          <tr>
            <td align="center" style="padding-top:20px;">
              <p style="color:#92400e;font-size:12px;margin:0;">
                © {{ date('Y') }} {{ config('app.name') }}. This is an automated message. Do not reply.
              </p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
