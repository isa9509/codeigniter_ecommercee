<?php defined('BASEPATH') OR exit('No direct script access allowed');
/*******************************************************************************
* Model dos produtos refatorado para evitar duplicação de código.
*******************************************************************************/
class Produtos_model extends CI_Model {

    public function __construct(){
        parent::__construct();
    }

    /**
     * Retorna os detalhes de um produto por ID ou hash MD5 do ID.
     * @param mixed $id ID numérico ou hash MD5 do produto
     * @return array
     */
    public function detalhes_produto($id){
        $this->db->where('id', $id);
        $this->db->or_where('md5(id)', $id);
        return $this->db->get('produtos')->result();
    }

    /**
     * Retorna os produtos em destaque para a home, ordenados aleatoriamente.
     * @param int $quantos Quantidade de produtos a retornar
     * @return array
     */
    public function destaques_home($quantos = 3){
        $this->db->limit($quantos);
        $this->db->order_by('id', 'random');
        return $this->db->get('produtos')->result();
    }

    /**
     * Busca produtos pelo título ou descrição.
     * @param string $buscar Termo de busca
     * @return array
     */
    public function busca($buscar){
        $this->db->group_start();
        $this->db->like('titulo', $buscar);
        $this->db->or_like('descricao', $buscar);
        $this->db->group_end();
        return $this->db->get('produtos')->result();
    }

    /**
     * Conta todos os produtos cadastrados.
     * @return int
     */
    public function contar(){
        return $this->db->count_all('produtos');
    }

    // Outros métodos de CRUD podem ser adicionados aqui, seguindo o mesmo padrão.
}