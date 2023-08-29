<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'firebase' => [
        'project_id' => 'kopi-sinarindo',
        'client_email' => 'firebase-adminsdk-1efze@kopi-sinarindo.iam.gserviceaccount.com',
        'private_key' => '-----BEGIN PRIVATE KEY-----\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQC9eiQOpOeVKkoi\nKnfu0gOlbyjKGNVHHLrUUO2ojloq7ivbqKqGxU5LhkUfWQK4VEhXVNpbpqZqB8bW\n8xKgRfHk+PYsCh7O4WrAdcP8hRfv6T4PeakLTqxfbPymm6LGw5ti7c+TdnJVW0fj\nMU7NotZEo4kuYNGCB8bWZrLi+wy83bASk25Qa3WjkIepfHuGJNLLw9flua9DwDIj\n0BQ+m72AAGsFqdADvmSI+JzNiDaCiozCYDc83P+5aG8GAdBnDKKxZ7YS0GO5c9fT\nGxN6cjgVvN8fMd4+pycGL/kVdfYcO2822fASbPeyFzPoSy5LUKXlPCepw5bALunu\nB20EX8+BAgMBAAECggEAMfe6+I2ucNz1LetrM3T49zDIXfuMizNdZc5tzky0JKwo\nrsXdKqtvyWUAZ5Lur7OyXC2JZDAfpMimPFtf5xpq/0pUiSqmE0LJBzZHWBm1RVSK\nRER+OvglZwjz2/AhTi+Zk85JNoc/AZmP7K89K+esf+9spulrdtIcMsBkno08P7X1\nv/AafB9cdCIkpUWKBTY843kZirOXW4R5ih4p1myDIPX2lENdS6kN5cKePSlWfBVN\n+hiwytyZZpMIHR9aDtMMTaTvMsb6qfPi8kxlex1uJp7MJyroZ+OCej4Ny4WOk9Zg\nNXofkUcwjRPnLaan0qcgUMjzb6GS0xXN1bhe70BpzQKBgQD5acY31wy5yt9yYWnG\nIfZ9lhqBS0LH1H+8RekExBKZ/c49iD3LRArxm6XB4r32IYaC61SiLGCaEBx4MwRl\nidMGPJF6NgyWpZIppdZG3czn0m/ceFNolKEkguZZGx5erDbcHRMCGrEty8pMunIQ\nJcICIlqmFbnd4xmW2W0hR92kdwKBgQDCeycHKRM99vWIpeBT6mfcs5SdoYrxsqZl\nvgfxveWHf8Ao7KXd7PJ7SK2M7xeEcyYqOnwZBLvoewCHr4XC72XppcXiKeeFjOOy\nQyQvm/7OHyytuAzfaWnpuIedFLt73bub215AMwfSi8arsyGSfggOXcTAmILZkvfk\nz3TESLOBxwKBgQCNGndm3gY4EntpxYzG6C4AjOw/26lTTnhZtp+G77qqXjnQ/AQh\nvEQIvor/bt0To5Hq/WJrQXoBjz3cDtjc7SVy8M9I+c0TaWaQo17fxtoHCTn53CUs\nFHI6KshQ/xOmcf2zd8tqNFmq1BGGaTDgy8u/01m/fqkhzDqM6kNgQb02LwKBgEoV\naa+TmQ3gtnWgYoz03S24huNpNymNGU/mjNYstXPhWUz9oM2iRlhqPhpStc2xo5cw\ngjdxkzcjK/eECFtSoKrZiED4H4bDPbWZV/5+2RihzX47f0PXvw3WSmqvDCBKPf5I\nGWYxSkiNEFg1u5M3SVBXreyD7Ex/bMkPsfZXj3HfAoGBAKhhsuuW1XwFmgp/2uPL\nGszObKjxT746iVYwVWtGx+lO2dn3w82ALKZLJrDaVmeUbnnPVQTqeuedBiemejo5\nksV2zAuP1xFMiKo8IqFwGHZ7qo4InkjG00iGrVVPxlUUhAxJKlvknA81z2LrpFZS\nztsQLBrZsWGsUm7y9BxJdpqD\n-----END PRIVATE KEY-----\n',
    ],
    

];
