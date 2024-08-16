@component('mail::message')

Olá,

Estamos te enviando um lembrete de que sua assinatura anual do Hacking Club será renovada em {{$expiresAt}}

Detalhes da sua assinatura<br>
Nome do produto: Assinatura Hacking Club<br>
Produtor(a): Crowsec Edtech<br>
Valor: R$ {{$planValue}}.<br>
Periodicidade: Assinatura anual

Você pode renovar através do pagamento em [boleto]({{$boletoUrl}}) ou você pode selecionar outra forma de pagamento em
[Gerencie suas assinaturas](https://app.hackingclub.com/profile/billing)

[PDF do boleto]({{$boletoPdf}})

Ficou com alguma dúvida? Entre em contato com a pessoa responsável por esta assinatura pelo email contato@crowsec.com.br

Abraços,
Equipe Hacking Club

@endcomponent