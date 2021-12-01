<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Vendas</title>
        <style>
            .page-break {
                page-break-after: always;
            }

            #customers {
                font-family: Arial, Helvetica, sans-serif;
                border-collapse: collapse;
                width: 100%;
            }

            #customers td, #customers th {
                border: 1px solid #ddd;
                padding: 8px;
            }

            #customers tr:nth-child(even){background-color: #f2f2f2;}

            #customers tr:hover {background-color: #ddd;}

            #customers th {
                padding-top: 10px;
                padding-bottom: 10px;
                background-color: #343a40;
                color: white;
            }
             
            th,td {
                text-align: center;
                align-items: center;
                vertical-align: middle;
            }
            
        </style>
    </head>
    <body>
        <table id="customers">
            <tr>
                <th>Cliente</th>
                <th>Qtd</th>
                <th>Produtos</th>
                <th>Valor Pago</th>
                <th>Valor Vendido</th>
                <th>Lucro</th>
            </tr>
            @foreach ($datas as $data)
                <tr>
                    <td>{{ $data->cliente['name'] }}</td>
                    <td>{{ $data->qtd_produto }}</td>
                    <td>
                        @foreach ($data->produtos as $value)
                            <span>{{ $value->name }}<span><br>
                        @endforeach
                    </td>
                    <td>{{ 'R$ '.number_format($data->pago, 2, ',', '.') }}</td>
                    <td>{{ 'R$ '.number_format($data->total_final, 2, ',', '.') }}</td>
                    <td>{{ 'R$ '.number_format($data->lucro, 2, ',', '.') }}</td>
                </tr>
            @endforeach
            
        </table>
    </body>
</html>