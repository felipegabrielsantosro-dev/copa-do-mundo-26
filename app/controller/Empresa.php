<?php

namespace app\controller;

use app\database\builder\InsertQuery;
use app\database\builder\SelectQuery;
use app\database\builder\DeleteQuery;

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
                'razao_social' => $sobrenome,
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
     public function Delete($request, $response)
    {
        try {
            $id = $_POST['id'];
            $IsDelete = DeleteQuery::table('empresa')
                ->where('id', '=', $id)
                ->delete();

            if (!$IsDelete) {
                echo json_encode(['status' => false, 'msg' => $IsDelete, 'id' => $id]);
                die;
            }
            echo json_encode(['status' => true, 'msg' => 'Removido com sucesso!', 'id' => $id]);
            die;
        } catch (\Throwable $th) {
            echo "Erro: " . $th->getMessage();
            die;
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
          2 => 'razao_social',  
          3 => 'cpf_cnpj',  
          4 => 'rg_ie',
        ];
        #Capturamos o nome do campo a ser odernado.
        $orderField = $fields[$order];
        #O termo pesquisado
        $term = $form ['search']['value'];
        $query = SelectQuery::select('id,nome_fantasia,razao_social,cpf_cnpj,rg_ie')->from('empresa');
        if (!is_null($term) && ($term !== '')) {
            $query->where('nome_fantasia', 'ilike', "%{$term}%", 'or')
            ->where('razao_social', 'ilike', "%{$term}%", 'or')
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
                $value['razao_social'],
                $value['cpf_cnpj'],
                $value['rg_ie'],
                "<button type='button' onclick='Editar(" . $value['id'] . ");' class='btn btn-warning'>
                <i class=\"bi bi-pen-fill\"></i>
                Editar
                </button>

                 <button type='button'  onclick='Delete(" . $value['id'] . ");' class='btn btn-danger'>
                 <i class=\"bi bi-trash-fill\"></i>
                 Excluir
                 </button>"
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
 