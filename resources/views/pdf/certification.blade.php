<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="https://use.typekit.net/uyx5kam.css?key=2">
    <style>
        body {
            background-color: #000;
            background-image: url("{{asset('img/bg-certification.png')}}");
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
        }
    </style>
</head>

<body>
    <div style="height: 1032px;width: 100%; display: -webkit-box">
        <div class="text-white" style="text-align: left; margin-top:300px; margin-left:60px; width: 50%; height:70%">
            <div style="margin-top: 30px; font-size: 28px; margin-bottom: 5px; font-family: 'proxima-nova, sans-serif';font-weight: 300; letter-spacing: 1px; font-style: nornal;">
                Certificamos que
            </div>
            <div style="margin-top: 30px; font-family: proxima-nova, sans-serif;font-weight: 800; font-size: 50px; text-transform: uppercase;">
                {{$student_name}}
            </div>
            <div style="margin-top: 30px; font-size: 28px; margin-bottom: 5px; font-family: 'proxima-nova, sans-serif';font-weight: 300; letter-spacing: 1px; font-style: nornal;">
                concluiu com êxito o Crowsec Web Hacking Initial e executou todos os passos necessários de um Pentest.
            </div>
            <div style="display:flex; align-items:center; margin-top: 150px">
                <div class="text-white" style="margin-left: 10px;">
                    <div style="font-family: proxima-nova, sans-serif;font-weight: 500; letter-spacing: 1px; font-size: 28px; ">
                        Carlos Eduardo Vieira
                    </div>
                    <div style="margin-top: -20px; font-weight: 100; font-size: 28px; ">
                        Avaliador Técnico
                    </div>
                </div>
                <div class="text-white" style="margin-left:90px;">
                    <div style="font-family: proxima-nova, sans-serif;font-weight: 500; letter-spacing: 1px; font-size: 28px; ">
                        Luciane Cichon Goes
                    </div>
                    <div style="margin-top: -20px; font-weight: 100; font-size: 28px; ">
                        CEO
                    </div>
                </div>
            </div>
            <div class="text-white" style=" margin-top:80px;">
                <div style="font-weight: 100;">
                    Valide o certificado em https://certification.crows
                </div>
                <div style="font-weight: 100;">
                    Código de segurança: <a href="https://certification.crowsec.com.br/?security_code={{$security_code}}" target="_blank" style="text-decoration: none;color: white">
                        {{$security_code}}
                </div>
                <div style="font-weight: 100;">
                    Emissão realizada em {{$issue_date}}
                </div>
            </div>
        </div>
    </div>
</body>

</html>