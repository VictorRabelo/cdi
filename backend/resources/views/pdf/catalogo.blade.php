<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Catálogo - Casa do Importado</title>
    <style type="text/css">
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .empty-message {
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .info-header {
            font-size: 9pt;
            text-align: end;
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 60px;
        }

        .logo .info-company {
            font-size: 9pt;
            margin-left: 10px;
            font-weight: bold;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }

        .catalog-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            max-width: 1000px;
            margin: 0 auto;
        }

        .product {
            width: calc(33.33% - 10px);
            max-width: 300px;
            margin-bottom: 30px;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: .25rem;
            box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075);
            display: flex;
            flex-direction: column;
            padding: 6px 0px;
        }

        .product .product-img {
            width: 100%;
            height: 130px;
            overflow: hidden;
        }

        .product img {
            width: 100%;
            height: 130px;
            object-fit: contain;
            border-top-left-radius: .25rem;
            border-top-right-radius: .25rem;
        }

        .product .card-body {
            padding: 5px;
        }

        .product .product-info {
            margin-top: auto;
            text-align: center;
        }

        .product .product-info h3 {
            margin: 0 0 .75rem;
            font-size: 1.25rem;
        }

        .product .product-info .price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #dc3545;
        }
    </style>

    <script type="text/javascript">
        window.onload = function() {
            window.print();
        }
    </script>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="{{ url('storage/appImgs/logo-preto.png') }}" alt="Logo">
                <div class="info-company">
                    <p>@casadoimportadogo</p>
                    <p>cdigoiania@outlook.com</p>
                    <p>(62) 98298-3642</p>
                </div>
            </div>
            <div class="info-header">
                <p>- Os preços podem sofrer alterações sem prévio aviso.</p>
                <p>- Os valores não incluem I.V.A.</p>
            </div>
        </div>
        <div class="catalog-container">
            @if (count($products) > 0)
            @foreach ($products as $item)
            <div class="product">
                <div class="product-img">
                    <img src="{{ url('/storage/'.$item->path) }}" alt="Produto">
                </div>
                <div class="product-info">
                    <h3>{{ $item->name }}</h3>
                    <span class="price">{{ 'R$ '.number_format($item->preco, 2, ',', '.') }}</span>
                </div>
            </div>
            @endforeach
            @else
            <div class="empty-message">
                <h3>Sem produtos no momento!<h3>
            </div>
            @endif
        </div>
    </div>
</body>

</html>