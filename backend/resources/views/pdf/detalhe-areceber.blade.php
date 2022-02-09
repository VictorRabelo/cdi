<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Detalhes da venda</title>
        <style>
            .page-break {
                page-break-after: always;
            }

            img {
                width: 50px;
                height: 50px;
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
             
            .margin {
                margin: 20px 0px;
            }
            
        </style>
    </head>
    <body>
        <table class="customers">
            <tr>
                <th colspan="4">Venda - {{date('d/m/Y H:i:s', strtotime($dadosVenda->created_at))}}</th>
            </tr>
            <tr>
                <th>Cliente</th>
                <th>Qtd</th>
                <th>Valor Pago</th>
                <th>Valor Vendido</th>
            </tr>
            <tr>
                <td>{{ $dadosVenda->cliente?$dadosVenda->cliente:'Cliente não informado' }}</td>
                <td>{{ $dadosVenda->qtd_produto }}</td>
                <td>{{ 'R$ '.number_format($dadosVenda->pago, 2, ',', '.') }}</td>
                <td>{{ 'R$ '.number_format($dadosVenda->total_final, 2, ',', '.') }}</td>
            </tr>
        </table>
        <hr>
        <div class="margin"></div>
        <table class="customers">
            <tr>
                <th colspan="5">Produtos da Venda</th>
            </tr>
            <tr>
                <th>#COD</th>
                <th>Foto</th>
                <th>Produto</th>
                <th>Qtd.</th>
                <th>Valor</th>
            </tr>
            @foreach ($dadosProdutos as $data)
                <tr>
                    <td>#{{ $data->produto->id_produto }}</td>
                    <td>
                        <img src="{{ $data->produto->path }}" alt="Produto">
                    </td>
                    <td>{{ $data->produto->name }}</td>
                    <td>{{ $data->qtd_venda }}</td>
                    <td>{{ 'R$ '.number_format($data->preco_venda, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </table>
        <hr>
        <div class="margin"></div>
        <table class="customers">
            <tr>
                <th colspan="3">Registro de movimentações</th>
            </tr>
            @if (count($dadosMovition) > 0)    
                <tr>
                    <th>#ID</th>
                    <th>Valor</th>
                    <th>Data</th>
                </tr>
                @foreach ($dadosMovition as $data)
                    <tr>
                        <td>{{ $data->id_movition }}</td>
                        <td>{{ 'R$ '.number_format($data->valor, 2, ',', '.') }}</td>
                        <td>{{ date('d/m/Y', strtotime($data->data)) }}</td>
                    </tr>
                @endforeach    
            @else
                <tr>
                    <th colspan="3">Não há movimentações no momento!</th>
                </tr>
            @endif
        </table>
    </body>
</html>