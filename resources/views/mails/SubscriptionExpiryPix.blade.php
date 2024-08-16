@component('mail::message')

Olá,

Estamos te enviando um lembrete de que sua assinatura anual do Hacking Club será renovada em {{$expiresAt}}

Detalhes da sua assinatura<br>
Nome do produto: Assinatura Hacking Club<br>
Produtor(a): Crowsec Edtech<br>
Valor: R$ {{$planValue}}.<br>
Periodicidade: Assinatura anual

Para sua  assinatura ser renovada automaticamente, será necessário o pagamento em pix ou você pode selecionar outra forma de pagamento em [Gerencie suas assinaturas](https://app.hackingclub.com/profile/billing)

Pix copia e cola:
{{$pixText}}

QR Code:

![QR Code]({{$pixQrcode}})

Ficou com alguma dúvida? Entre em contato com a pessoa responsável por esta assinatura pelo email contato@crowsec.com.br

Abraços,
Equipe Hacking Club

@endcomponent