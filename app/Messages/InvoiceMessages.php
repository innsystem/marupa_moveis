<?php

return [
    'email' => [
        'invoice_new' => [
            'subject' => 'Nova Fatura Gerada - #{{invoice_id}}',
            'body' => 'Olá {{name}}!<br>Foi gerada uma fatura #{{invoice_id}} que vence em {{invoice_due_date}}.<br><br><b>Serviço(s) Contratado(s):</b><br>{{services}}<br><br><b>Valor Total R$ {{invoice_total}}</b><br><br>',
        ],
        'invoice_new_pix' => [
            'subject' => 'Nova Fatura Gerada - #{{invoice_id}}',
            'body' => 'Olá {{name}}!<br>Foi gerada uma fatura #{{invoice_id}} que vence em {{invoice_due_date}}.<br><br><b>Serviço(s) Contratado(s):</b><br>{{services}}<br><br><b>Valor Total R$ {{invoice_total}}</b><br><br>' .
                '<b>Pagamento via PIX:</b><br>Código PIX: <pre>{{pix_qr_code}}</pre><br>' .
                '<img src="{{pix_qr_code_image}}" alt="QR Code PIX" style="width:200px;height:200px;"><br>',
        ],
        'invoice_new_boleto' => [
            'subject' => 'Nova Fatura Gerada - #{{invoice_id}}',
            'body' => 'Olá {{name}}!<br>Foi gerada uma fatura #{{invoice_id}} que vence em {{invoice_due_date}}.<br><br><b>Serviço(s) Contratado(s):</b><br>{{services}}<br><br><b>Valor Total R$ {{invoice_total}}</b><br><br>' .
                '<b>Pagamento via Boleto:</b><br>Linha digitável: <pre>{{boleto_digitable_line}}</pre><br>' .
                '<a href="{{boleto_pdf}}" target="_blank">Abrir Boleto em PDF</a><br>',
        ],
        'payment_confirmed' => [
            'subject' => 'Pagamento Confirmado!',
            'body' => 'Seu pagamento da fatura N° {{invoice_id}} foi confirmado.',
        ],
        'invoice_reminder' => [
            'subject' => 'Lembrete de Fatura',
            'body' => 'Sua fatura N° {{invoice_id}} vence em {{due_date}}.',
        ],
    ],

    'whatsapp' => [
        'invoice_new' => "Olá {{name}}!\nFoi gerado uma fatura #{{invoice_id}} que vence em {{invoice_due_date}}.\n\n*Serviço(s) Contratado(s):*\n{{services}}\n*Valor Total R$ {{invoice_total}}*",

        'invoice_new_pix_image' => "{{image}}",
        'invoice_new_pix_code' => "{{qr_code}}",

        'invoice_new_boleto_text' => "{{name}}, aqui está o boleto e a linha digitável do seu boleto.",
        'invoice_new_boleto_code' => "{{boleto_digitable_line}}",
        'invoice_new_boleto_url' => "{{document_pdf}}",
        
        'payment_confirmed' => 'Olá {{name}}, seu pagamento da fatura N° {{invoice_id}} foi confirmado!',
        'invoice_reminder' => 'Olá {{name}}, sua fatura N° {{invoice_id}} vence em {{due_date}}. ',
    ],
];
