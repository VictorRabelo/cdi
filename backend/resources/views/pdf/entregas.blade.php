<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Entregas</title>
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
                <th colspan="5">Entregas - {{date('d/m/Y', strtotime($data_now))}}</th>
            </tr>
            <tr>
                <th>Entregador</th>
                <th>Qtd. de Produtos</th>
                <th>Lucro</th>
                <th>Valor Total</th>
                <th>Status</th>
            </tr>
            @foreach ($datas as $data)
                <tr>
                    <td>{{ $data->entregador->name }}</td>
                    <td>{{ $data->qtd_produtos }}</td>
                    <td>{{ 'R$ '.number_format($data->lucro, 2, ',', '.') }}</td>
                    <td>{{ 'R$ '.number_format($data->total_final, 2, ',', '.') }}</td>
                    <td style="color: #fff;" bgcolor="{{($data->status !== 'ok')?'#0000ff':'#008000'}}">
                        {{ $data->status }}
                    </td>
                </tr>
            @endforeach
            
        </table>
    </body>
</html>