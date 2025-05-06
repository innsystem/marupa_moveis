<?php

return [
    'email' => [
        'welcome' => [
            'subject' => 'Bem-vindo ao sistema',
            'body' => 'Olá {{name}}, seja bem-vindo!',
        ],
        'password_recovery' => [
            'subject' => 'Recuperação de Senha',
            'body' => 'Foi solicitada uma recuperação de senha para seu e-mail. <br> Digite o código abaixo para redefinir sua senha: <br> {{password_code}}',
        ],
    ],

    'whatsapp' => [
        'welcome' => 'Olá {{name}}, seja bem-vindo!',
        'password_recovery' => "Olá {{name}}, foi solicitada uma recuperação de senha para seu e-mail. \nDigite o código abaixo para redefinir sua senha: \n*{{password_code}}*",

        'product_send_image' => "{{image}}",
        'product_send_link' => "{{link}}",

        'product_send_image_group' => "{{image}}",
        'product_send_link_group' => "✨ *{{title}}* \n\n🔸 Preço: R$ {{price}} \n📲 Compre direto na *{{integration_name}}*\n{{link}}",
    ],
];
