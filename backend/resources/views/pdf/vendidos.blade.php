<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Vendidos</title>
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
             
            img {
                width: 50px;
                height: 50px;
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
                <th colspan="3">Qtd. Vendidas</th>
            </tr>
            <tr>
                <th>Foto</th>
                <th>Produto</th>
                <th>Qtd. Vendida</th>
            </tr>
            @foreach ($products as $item)
                <tr>
                    <td>
                        <img src="{{ $item->path }}" alt="Produto">
                    </td>
                    <td>{{ $data->nameProduto }}</td>
                    <td>{{ $data->qtdTotal }}</td>
                </tr>
            @endforeach
        </table>
    </body>
</html>