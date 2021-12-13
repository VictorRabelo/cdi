<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Detelhes da entrega</title>
        <style>
            .page-break {
                page-break-after: always;
            }

            .customers {
                font-family: Arial, Helvetica, sans-serif;
                border-collapse: collapse;
                width: 100%;
            }

            .customers td, .customers th {
                border: 1px solid #ddd;
                padding: 8px;
            }

            .customers th {
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
        <table class="customers">
            <tr>
                <th colspan="5">Detelhes da entrega - {{date('d/m/Y', strtotime($dadosEntrega->updated_at))}}</th>
            </tr>
            <tr>
                <th>Entregador</th>
                <th>Qtd. de Produtos</th>
                <th>Lucro</th>
                <th>Valor Total</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>{{ $dadosEntrega->entregador }}</td>
                <td>{{ $dadosEntrega->qtd_produtos }}</td>
                <td>{{ 'R$ '.number_format($dadosEntrega->lucro, 2, ',', '.') }}</td>
                <td>{{ 'R$ '.number_format($dadosEntrega->total_final, 2, ',', '.') }}</td>
                <td style="color: #fff;" bgcolor="{{($dadosEntrega->status !== 'ok')?'#0000ff':'#008000'}}">
                    {{ $dadosEntrega->status }}
                </td>
            </tr>
            <tr>
                <td colspan="5"></td>
            </tr>
            <tr>
                <th colspan="5">Produtos da entrega</th>
            </tr>
            <tr>
                <th>#COD</th>
                <th>Qtd.</th>
                <th>Foto</th>
                <th>Valor</th>
                <th>Lucro</th>
            </tr>
            @foreach ($dadosProdutos as $data)
                <tr>
                    <td>#{{ $data->produto->id_produto }}</td>
                    <td>{{ $data->qtd_produto }}</td>
                    <td>{{ $data->produto->name }}</td>
                    <td>{{ 'R$ '.number_format($data->preco_entrega, 2, ',', '.') }}</td>
                    <td>{{ 'R$ '.number_format($data->lucro_entrega, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </table>
        <div class="page-break"></div>
        <table class="customers">
            <tr>
                <th colspan="6">Vendas - {{ $dadosEntrega->entregador }} - {{date('d/m/Y', strtotime($data_now))}}</th>
            </tr>
            @if (count($dadosVendas) > 0)    
                <tr>
                    <th>Cliente</th>
                    <th>Qtd</th>
                    <th>Produtos</th>
                    <th>Valor Pago</th>
                    <th>Valor Vendido</th>
                    <th>Lucro</th>
                </tr>
                @foreach ($dadosVendas as $data)
                    <tr>
                        <td>{{ $data->cliente['name']?$data->cliente['name']:'Cliente não informado' }}</td>
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
            @else
                <tr>
                    <th colspan="6">Não há vendas!</th>
                </tr>
            @endif
        </table>
    </body>
</html>