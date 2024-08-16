<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="https://use.typekit.net/uyx5kam.css?key=2">
    <style>
        body {
            background-color: #000;
            background-image: url({{asset('img/bg-cert.png')}});
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
        }

        #content {
            -webkit-transform: rotate(-90deg);
            font-size: 18px;
            position: fixed;
            bottom: 300px;
            left: -272px;
            margin-left: 8px;
            text-align: center;
            width: 600px;
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <div style="height: 991px;width: 100%; display: -webkit-box">
        <div class="text-white" style="height: 100%;background: #0064ff; width: 5%;text-align: center;">
            <div id="content">Código de segurança: <a
                    href="https://certification.crowsec.com.br/?security_code={{$security_code}}" target="_blank"
                    style="text-decoration: none;color: white"><b>{{$security_code}}</b></a> / Emissão realizada em
                {{$issue_date}}</div>
        </div>
        <div class="text-white" style="width: 95%;text-align: center; margin-top:45px;">
            <div id="title"
                style="font-family: proxima-nova, sans-serif;font-weight: 500; font-size: 100px; font-style: nornal;color: #0064ff">
                CROWSEC</div>
            <div id="cert-name"
                style="font-family: proxima-nova, sans-serif;font-weight: 300; letter-spacing: 1px ; font-size: 50px; margin-top: -25px; font-style: nornal;color: #fff">
                {{$cert_name}}</div>
            <div
                style="margin-top: 50px; font-size: 20px; margin-bottom: 5px; font-family: 'proxima-nova, sans-serif';font-weight: 300; letter-spacing: 1px; font-style: nornal;">
                Certificamos que</div>
            <div
                style="font-family: proxima-nova, sans-serif;font-weight: 800; font-size: 50px; text-transform: uppercase;">
                {{$student_name}}</div>
            <div
                style="margin-top: 5px; margin-right: 15px; margin-left: 15px; font-size: 20px; font-family: 'proxima-nova, sans-serif';font-weight: 300; font-style: nornal; letter-spacing: 1px;">
                {{$cert_description}}</div>
            <div id="sign" class=" text-center"
                style="margin-top: 15px;display: -webkit-box;align-content: center;-webkit-box-align: center;-webkit-box-pack: center;">
                <div style="width: 33%;margin-top: -50px;" class="sign">
                    <div>
                        <img src="{{asset('img/as-kadu.png')}}" style="width: 100px;" alt="">
                    </div>
                    <div
                        style="font-family: proxima-nova, sans-serif;font-weight: 500; letter-spacing: 1px; color:#0064ff; font-size: 14px;">
                        CARLOS EDUARDO VIEIRA</div>
                    <div style="margin-top: -20px;font-weight: 100;">______________________</div>
                    <div>Avaliador técnico</div>
                </div>
                <div style="width: 33%;margin-top: -10px;" class="logo">
                    <img src="{{asset('img/logo.png')}}" style="width: 100%;" alt="">
                </div>
                <div style="width: 33%;margin-top: -50px;" class="sign">
                    <div>
                        <img src="{{asset('img/as-lu.png')}}" style="width: 150px;" alt="">
                    </div>
                    <div
                        style="margin-top: -40px; font-family: proxima-nova, sans-serif;font-weight: 500; letter-spacing: 1px; color:#0064ff; font-size: 14px;">
                        LUCIANE CICHON GOES</div>
                    <div style="margin-top: -20px; font-weight: 100;">______________________</div>
                    <div>CEO</div>
                </div>
            </div>
            <div>
                <p class="text-white" style="position: fixed; bottom: 1px; left: 7%; font-size: 15px; opacity: 1; width: 95%; text-align: left;">
                    Valide o certificado em https://app.hackingclub.com/certitifcate/validate</p>
            </div>
        </div>
    </div>
</body>

</html>