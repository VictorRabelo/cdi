<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Catálogo de Produtos Casa do Importado</title>
        <style>
            @page {
                margin: 5px 0cm;
            }

            @page {
                margin: 0cm 0cm;
            }

            /** Define now the real margins of every page in the PDF **/
            body {
                margin-top: 2cm;
                margin-left: 2cm;
                margin-right: 2cm;
                margin-bottom: 2cm;
            }

            /** Define the header rules **/
            header {
                position: fixed;
                top: 0cm;
                left: 0cm;
                right: 0cm;
                height: 2cm;
                /** Extra personal styles **/
                text-align: center;
                line-height: 1.5cm;
            }

            /** Define the footer rules **/
            footer {
                position: fixed; 
                bottom: 0cm; 
                left: 0cm; 
                right: 0cm;
                height: 1cm;

                /** Extra personal styles **/
                text-align: center;
                line-height: 0.8cm;
                font-size: 15pt;
                border-top: 1px solid  #5D6975;
                background: url({{ storage_path('app/public/appImgs/dimension.png') }});
            }

            .page-break {
                page-break-before: always;
            }

            img {
                width: 130px;
                height: 130px;
            }

            #logo {
                float: left;
            }
            
            #contato {
                float: right;
            }
            
            #logo img {
                width: 100px;
                height: 100px;
            }

            h1 {
                border-top: 1px solid  #5D6975;
                border-bottom: 1px solid  #5D6975;
                color: #5D6975;
                font-size: 2.0em;
                line-height: 1.4em;
                font-weight: normal;
                text-align: center;
                padding: 8px 0px;
                margin: 0 0 20px 0;
                background: url({{ storage_path('app/public/appImgs/dimension.png') }});
            }
            .customers {
                margin: 20px 0px;
                font-family: Arial, Helvetica, sans-serif;
                border-collapse: collapse;
                border-spacing: 0;
                width: 100%;
            }

            .customers tr:nth-child(2n-1) td {
                background: #F5F5F5;
            }

            .customers td, .customers th {
                padding: 12px;
            }

            .customers th {
                padding-top: 10px;
                padding-bottom: 10px;
                background-color: #343a40;
                color: white;
            }
             
            th,td {
                text-align: center;
                font-size: 15pt;
                align-items: center;
                vertical-align: middle;
            }
             
            hr {
                margin: 20px 0px;
            }
        </style>
    </head>
    <body>
        <header>
            <div id="logo">
                <img src="{{ storage_path('app/public/appImgs/logo-cdi.png') }}" alt="Produto">
            </div>
            <h1>Catálogo - Casa do Importado</h1>
        </header>

        <footer>
            @casadoimportadogo | (62) 98298-3642 | casadoimportadogo@outlook.com
        </footer>

        <table class="customers">
            @if (count($products) > 0)    
                @foreach ($products as $item)
                    <tr>
                        <td>
                            <img src="{{ storage_path('app/public/'.$item->path) }}" alt="Produto">
                        </td>
                        <td>
                            <h3>{{ $item->name }}</h3>
                            <p>{{ 'R$ '.number_format($item->preco, 2, ',', '.') }}</p>
                        </td>
                    </tr>
                @endforeach    
            @else
                <tr>
                    <th colspan="2">Não há produtos no momento!</th>
                </tr>
            @endif
        </table>
    </body>
</html>