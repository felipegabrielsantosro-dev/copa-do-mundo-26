<?php

namespace app\controller;

use app\database\builder\InsertQuery;
use app\database\builder\DeleteQuery;
use app\database\builder\SelectQuery;

class User extends Base
{

    public function lista($request, $response)
    {
        $dadosTemplate = [
            'titulo' => 'Lista de usuário'
        ];
        return $this->getTwig()
            ->render($response, $this->setView('listuser'), $dadosTemplate)
            ->withHeader('Content-Type', 'text/html')
            ->withStatus(200);
    }
    public function cadastro($request, $response)
    {
        $dadosTemplate = [
            'titulo' => 'Cadastro de usuário'
        ];
        return $this->getTwig()
            ->render($response, $this->setView('user'), $dadosTemplate)
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
            $data_nascimento = $_POST['data_nascimento'];
            $senha = $_POST['senha'];
                        
            $FieldsAndValues = [
                'nome' => $nome,
                'sobrenome' => $sobrenome,
                'cpf' => $cpf,
                'rg' => $rg,
                'data_nascimento' => $data_nascimento,
                'senha' => $senha
            ];

            $IsSave = InsertQuery::table('usuario')->save($FieldsAndValues);
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
    public function delete($request, $response)
    {
        try {
            $id = $_POST['id'];
            $IsDelete = DeleteQuery::table('usuario')
                ->where('id', '=', $id)
                ->delete();

            if (!$IsDelete) {
                echo 'Erro ao deletar';
                die;
            }
            echo "Deletado com sucesso!";
            die;
        } catch (\Throwable $th) {
            echo "Erro: " . $th->getMessage();
            die;
        }
    }
    public function listuser($request, $response){
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
          1 => 'nome',  
          2 => 'sobrenome',  
          3 => 'cpf',  
          4 => 'rg', 
        ];
        #Capturamos o nome do campo a ser odernado.
        $orderField = $fields[$order];
        #O termo pesquisado
        $term = $form ['search']['value'];
        $query = SelectQuery::select('id,nome,sobrenome,cpf,rg,data_nascimento')->from('usuario');
        if (!is_null($term) && ($term !== '')) {
            $query->where('nome', 'ilike', "%{$term}%", 'or')
            ->where('sobrenome', 'ilike', "%{$term}%", 'or')
            ->where('cpf', 'ilike', "%{$term}%", 'or')
            ->where('rg', 'ilike', "%{$term}%");
        }
        $users = $query
        ->order($orderField, $orderType)
        ->limit($length, $start)
        ->fetchAll();
        $userData = [];
        foreach($users as $key => $value) {
            $userData[$key] = [
                $value['id'],
                $value['nome'],
                $value['sobrenome'],
                $value['cpf'],
                $value['rg'],
                "<button class='btn btn-warning'>Editar</button>
                <button class='btn btn-danger'>Excluir</button>"
            ];
        }
        $data = [
            'status' => true,
            'recordsTotal' => count($users),
            'recordsFiltered' => count($users),
            'data' => $userData
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
