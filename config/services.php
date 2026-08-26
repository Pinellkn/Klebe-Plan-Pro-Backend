<?php

// À FUSIONNER dans config/services.php du projet principal (tâche Bilal —
// WhatsApp & rappels). Si le fichier existe déjà, ajouter seulement la
// clé 'whatsapp' au tableau retourné, ne pas écraser les autres services.

return [

    /*
    |----------------------------------------------------------------------
    | WhatsApp Cloud API (Meta)
    |----------------------------------------------------------------------
    | Utilisé par App\Services\WhatsAppService pour envoyer les 3 rappels
    | automatiques (veille 18h, jour J 8h, 15 min avant) au DG.
    | Credentials à récupérer sur developers.facebook.com (Meta for
    | Developers > WhatsApp > API Setup).
    */
    'whatsapp' => [
        'token' => env('WHATSAPP_TOKEN'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'api_version' => env('WHATSAPP_API_VERSION', 'v21.0'),
    ],

];
