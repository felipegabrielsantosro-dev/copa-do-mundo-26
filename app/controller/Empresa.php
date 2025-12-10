<?php

namespace app\controller;

use app\database\builder\InsertQuery;
use app\database\builder\SelectQuery;

class Empresa extends Base
{

    public function lista($request, $response)
    {
        $dadosTemplate = [
            'titulo' => 'Lista de Empresas'
        ];
        return $this->getTwig()
            ->render($response, $this->setView('listempresa'), $dadosTemplate)
            ->withHeader('Content-Type', 'text/html')
            ->withStatus(200);
    }
    public function cadastro($request, $response)
    {
        $dadosTemplate = [
            'titulo' => 'Cadastro de Empresas'
        ];
        return $this->getTwig()
            ->render($response, $this->setView('empresa'), $dadosTemplate)
            ->withHeader('Content-Type', 'text/html')
            ->withStatus(200);
    }
    public function insert($request, $response)
    {
        try {
            $nome = $_POST['nome'];
            $sobrenome = $_POST['sobrenome'];
            $cpf = $_POST['cpf'];
            $rg = $_POST['rg'];
            
            
            $FieldsAndValues = [
                'nome_fantasia' => $nome,
                'sobrenome_razao' => $sobrenome,
                'cpf_cnpj' => $cpf,
                'rg_ie' => $rg
            ];

            $IsSave = InsertQuery::table('empresa')->save($FieldsAndValues);
            if (!$IsSave) {
                echo 'Erro ao salvar';
                die;
            }
            echo "Salvo com sucesso!";
            die;
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
        public function listempresa($request, $response){
        #Captura todas a variaveis de forma mais segura VARIAVEIS POST.
        $form = $request->getParsedBody();
        #Qual a coluna da tabela deve ser ordenada.
        $order = $form['order'][0]['column'];
        #Tipo de ordenação
        $orderType = $form['order'][0]['dir'];
        #Em qual registro se inicia o retorno dos registros, OFFSET
        $start = $form['start'];
        #Limite de registro a serem retornados do banco de dados LIMIT
        $length = $form['length'];
        $fields= [
          0 => 'id',  
          1 => 'nome_fantasia',  
          2 => 'sobrenome_razao',  
          3 => 'cpf_cnpj',  
          4 => 'rg_ie',
        ];
        #Capturamos o nome do campo a ser odernado.
        $orderField = $fields[$order];
        #O termo pesquisado
        $term = $form ['search']['value'];
        $query = SelectQuery::select('id,nome_fantasia,sobrenome_razao,cpf_cnpj,rg_ie')->from('empresa');
        if (!is_null($term) && ($term !== '')) {
            $query->where('nome_fantasia', 'ilike', "%{$term}%", 'or')
            ->where('sobrenome_razao', 'ilike', "%{$term}%", 'or')
            ->where('cpf_cnpj', 'ilike', "%{$term}%", 'or')
            ->where('rg_ie', 'ilike', "%{$term}%");
        }
        $empresa = $query
        ->order($orderField, $orderType)
        ->limit($length, $start)
        ->fetchAll();
        $empresaData = [];
        foreach($empresa as $key => $value) {
            $empresaData[$key] = [
                $value['id'],
                $value['nome_fantasia'],
                $value['sobrenome_razao'],
                $value['cpf_cnpj'],
                $value['rg_ie'],
                "<button class='btn btn-warning'>Editar</button>
                <button class='btn btn-danger'>Excluir</button>"
            ];
        }
        $data = [
            'status' => true,
            'recordsTotal' => count($empresa),
            'recordsFiltered' => count($empresa),
            'data' => $empresaData
        ];
        $payload = json_encode($data);

        $response->getBody()->write($payload);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
        /*
        */
    }
}
 