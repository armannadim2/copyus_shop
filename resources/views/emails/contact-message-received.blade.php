<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="utf-8">
    <title>Nou missatge de contacte</title>
</head>
<body style="margin:0; padding:24px; background:#efede6; font-family: 'Outfit', Arial, sans-serif; color:#181818;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0"
           style="max-width:640px; margin:0 auto; background:#ffffff;
                  border-radius:16px; border:1px solid rgba(24,24,24,0.06);">
        <tr>
            <td style="background:#5F75F4; padding:24px 32px; border-radius:16px 16px 0 0;">
                <p style="margin:0; font-size:11px; letter-spacing:0.2em;
                          text-transform:uppercase; color:rgba(255,255,255,0.85);">
                    COPYUS · Missatge de contacte
                </p>
                <h1 style="margin:6px 0 0; font-family: 'Alumni Sans', Arial, sans-serif;
                           font-weight:400; font-size:28px; color:#ffffff; line-height:1.2;">
                    {{ $contactMessage->subject }}
                </h1>
            </td>
        </tr>

        <tr>
            <td style="padding:28px 32px 8px;">
                <p style="margin:0 0 16px; font-size:14px; color:#181818;">
                    S'ha rebut un nou missatge des del formulari de contacte.
                    Pots respondre directament a aquest correu per contactar amb el remitent.
                </p>

                <h2 style="font-family:'Alumni Sans', Arial, sans-serif;
                           font-weight:700; font-size:20px;
                           margin:24px 0 12px; color:#181818;">
                    Remitent
                </h2>
                <table width="100%" cellpadding="0" cellspacing="0" border="0"
                       style="font-size:14px; line-height:1.5;">
                    <tr>
                        <td width="120" style="padding:6px 0; color:rgba(24,24,24,0.6);">Nom</td>
                        <td style="padding:6px 0; color:#181818;">{{ $contactMessage->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0; color:rgba(24,24,24,0.6);">Correu</td>
                        <td style="padding:6px 0;">
                            <a href="mailto:{{ $contactMessage->email }}"
                               style="color:#5F75F4; text-decoration:none;">
                                {{ $contactMessage->email }}
                            </a>
                        </td>
                    </tr>
                    @if($contactMessage->phone)
                    <tr>
                        <td style="padding:6px 0; color:rgba(24,24,24,0.6);">Telèfon</td>
                        <td style="padding:6px 0;">
                            <a href="tel:{{ $contactMessage->phone }}"
                               style="color:#5F75F4; text-decoration:none;">
                                {{ $contactMessage->phone }}
                            </a>
                        </td>
                    </tr>
                    @endif
                </table>

                <h2 style="font-family:'Alumni Sans', Arial, sans-serif;
                           font-weight:700; font-size:20px;
                           margin:24px 0 12px; color:#181818;">
                    Missatge
                </h2>
                <div style="background:#efede6; padding:18px; border-radius:12px;
                            font-size:14px; line-height:1.6; color:#181818;
                            white-space:pre-line;">{{ $contactMessage->message }}</div>

                <p style="margin:32px 0 8px; font-size:13px; color:rgba(24,24,24,0.55);">
                    Pots gestionar aquest missatge al panell d'administració.
                </p>
            </td>
        </tr>

        <tr>
            <td style="padding:16px 32px 24px; border-top:1px solid rgba(24,24,24,0.06);
                       font-size:12px; color:rgba(24,24,24,0.45);">
                COPYUS Impressió Digital · Parc TecnoCampus, Mataró
            </td>
        </tr>
    </table>

</body>
</html>
