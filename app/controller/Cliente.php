<?php

namespace app\controller;

use app\database\builder\InsertQuery;
use app\database\builder\DeleteQuery;
use app\database\builder\SelectQuery;

class Cliente extends Base
{
    public function lista($request, $response)
    {
        try {
            $dadosTemplate = [
                'titulo' => 'Lista de Clientes'
            ];

            return $this->getTwig()
                ->render($response, $this->setView('listcliente'), $dadosTemplate)
                ->withHeader('Content-Type', 'text/html')
                ->withStatus(200);

        } catch (\Exception $e) {
            echo "Erro: " . $e->getMessage();
        }
    }
    public function cadastro($request, $response)
    {
        try {
            $dadosTemplate = [
                'titulo' => 'Cadastro de Cliente'
            ];

            return $this->getTwig()
                ->render($response, $this->setView('cliente'), $dadosTemplate)
                ->withHeader('Content-Type', 'text/html')
                ->withStatus(200);

        } catch (\Exception $e) {
            echo "Erro: " . $e->getMessage();
        }
    }
    public function insert($request, $response)
    {
        try {
            $nome      = $_POST['nome'] ?? null;
            $sobrenome = $_POST['sobrenome'] ?? null;
            $cpf       = $_POST['cpf'] ?? null;
            $rg        = $_POST['rg'] ?? null;

            $FieldsAndValues = [
                'nome_completo'   => $nome_completo = $nome . ' ' . $sobrenome,
                'cpf_cnpj'        => $cpf,
                'rg_ie'           => $rg,
            ];

            $IsSave = InsertQuery::table('cliente')->save($FieldsAndValues);

            if (!$IsSave) {
                echo 'Erro ao salvar';
                die;
            }

            echo "Salvo com sucesso!";
            die;

        } catch (\Throwable $th) {
            echo "Erro: " . $th->getMessage();
            die;
        }
    }
    public function Delete($request, $response)
    {
        try {
            $id = $_POST['id'];
            $IsDelete = DeleteQuery::table('cliente')
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
    public function listcliente($request, $response)
    {
        $form = $request->getParsedBody();

        # Campos e ordenação
        $order     = $form['order'][0]['column'];
        $orderType = $form['order'][0]['dir'];
        $start     = $form['start'];
        $length    = $form['length'];

        $fields = [
            0 => 'id',
            1 => 'nome_completo',
            2 => 'cpf_cnpj',
            3 => 'rg_ie'
        ];

        $orderField = $fields[$order];
        $term       = $form['search']['value'];

        # Query base
        $query = SelectQuery::select('id,nome_completo,cpf_cnpj,rg_ie')
            ->from('cliente');

        # Filtro
        if (!is_null($term) && $term !== '') {
            $query->where('nome_completo', 'ilike', "%{$term}%", 'or')
                ->where('cpf_cnpj', 'ilike', "%{$term}%", 'or')
                ->where('rg_ie', 'ilike', "%{$term}%");
        }

        # Paginação + ordenação
        $clients = $query
            ->order($orderField, $orderType)
            ->limit($length, $start)
            ->fetchAll();

        # Monta array nos padrões DataTables
        $clientsData = [];

        foreach ($clients as $key => $value) {
            $clientsData[$key] = [
                $value['id'],
                $value['nome_completo'],
                $value['cpf_cnpj'],
                $value['rg_ie'],

                "<a href=\"/cliente/editar/" . $value['id'] . "\" class=\"btn btn-warning\">Editar</a>

                <button type='button'  onclick='Delete(" . $value['id'] . ");' class='btn btn-danger'>
                 <i class=\"bi bi-trash-fill\"></i>
                 Excluir
                 </button>"
            ];
        }

        # Resposta
        $data = [
            'status'          => true,
            'recordsTotal'    => count($clients),
            'recordsFiltered' => count($clients),
            'data'            => $clientsData
        ];

        $payload = json_encode($data);

        $response->getBody()->write($payload);

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
     public function alterar($request, $response, $args)
    {
        try {
            $id = $args['id'];
            $user = SelectQuery::select()->from('usuario')->where('id', '=', $id)->fetch();
            $dadosTemplate = [
                'acao' => 'e',
                'id' => $id,
                'titulo' => 'Cadastro e edição',
                'user' => $user
            ];
            return $this->getTwig()
                ->render($response, $this->setView('user'), $dadosTemplate)
                ->withHeader('Content-Type', 'text/html')
                ->withStatus(200);
        } catch (\Exception $e) {
            var_dump($e);
        }
    }
}
