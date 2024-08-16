@component('mail::message')

Olá, 

A cobrança do seu pagamento anual à assinatura do Hacking Club foi realizada com sucesso.

Detalhes:<br>
Data da próxima cobrança: {{$expiresAt}}<br>
Valor: R$ {{$planValue}}<br>
Parcelamento: {{$installmentsNumber}}

Se você tiver algum problema de acesso ou quiser tirar dúvidas sobre o conteúdo desta assinatura, entre em contato com a pessoa responsável por ela através do e-mail contato@crowsec.com.br.

Abraços,
Equipe Hacking Club

@endcomponent