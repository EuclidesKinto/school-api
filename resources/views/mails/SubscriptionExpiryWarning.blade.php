@component('mail::message')

Olá,

Estamos te enviando um lembrete de que sua assinatura anual do Hacking Club será renovada em {{$expiresAt}}.

Detalhes da sua assinatura<br>
Nome do produto: Assinatura Hacking Club<br>
Produtor(a): Crowsec Edtech<br>
Valor: R$ {{$planValue}}.<br>
Periodicidade: Assinatura anual

Sua assinatura será renovada automaticamente em {{$expiresAt}} a menos que ela seja cancelada antes dessa data. 

[Gerencie suas assinaturas](https://app.hackingclub.com/profile/billing)

Ficou com alguma dúvida? Entre em contato com a pessoa responsável por esta assinatura pelo email contato@crowsec.com.br

Abraços,<br>
Equipe Hacking club

@endcomponent