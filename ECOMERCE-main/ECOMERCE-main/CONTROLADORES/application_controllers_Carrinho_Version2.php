<?php defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************************************
* Controlador do carrinho de compras.
**********************************************************************************/
class Carrinho extends CI_Controller
{
    private $categorias;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('modelo_categorias', 'modelcategorias');
        $this->categorias = $this->modelcategorias->listar_categorias();
    }

    public function index()
    {
        $dados_cabecalho['categorias'] = $this->categorias;

        if (null !== $this->session->userdata('logado') && count($this->cart->contents()) > 0) {
            $sessao = $this->session->userdata();
            $cep = str_replace("-", "", $sessao['cliente']->cep);
            $dados['frete'] = $this->calcular_frete($cep);
            // $estado = $sessao['cliente']->estado; // Usar para método de cálculo por transportadora.
            // $dados['frete'] = $this->frete_transportadora($estado);
        } else {
            $dados['frete'] = null;
        }

        $this->load->view('cabecalho-html');
        $this->load->view('cabecalho', $dados_cabecalho);
        $this->load->view('carrinho', $dados);
        $this->load->view('rodape');
        $this->load->view('rodape-html');
    }
	
	public function formulario_pagamento()
    {
        $dados_cabecalho['categorias'] = $this->categorias;
        if (null !== $this->session->userdata('logado')) {
            $sessao = $this->session->userdata();
            $cep = str_replace("-", "", $sessao['cliente']->cep);
            $dados['frete'] = $this->calcular_frete($cep);
        } else {
            $dados['frete'] = null;
        }
        $this->load->view('cabecalho-html');
        $this->load->view('cabecalho', $dados_cabecalho);
        $this->load->view('carrinho-formulario-pagamento', $dados);
        $this->load->view('rodape');
        $this->load->view('rodape-html');
    }

    public function adicionar()
    {
        $dados = array(
            'id'        => $this->input->post('id'),
            'qty'       => $this->input->post('quantidade'),
            'price'     => $this->input->post('preco'),
            'name'      => $this->input->post('nome'),
            'altura'    => $this->input->post('altura'),
            'largura'   => $this->input->post('largura'),
            'comprimento' => $this->input->post('comprimento'),
            'peso'      => $this->input->post('peso'),
            'options'   => null,
            'url'       => $this->input->post('url'),
            'foto'      => $this->input->post('foto')
        );
        $this->cart->insert($dados);
        redirect(base_url("carrinho"));
    }

    public function atualizar()
    {
        foreach ($this->input->post() as $item) {
            if (isset($item['rowid'])) {
                $dados = array('rowid' => $item['rowid'], 'qty' => $item['qty']);
                $this->cart->update($dados);
            }
        }
        redirect(base_url('carrinho'));
    }

    public function remover($rowid)
    {
        $dados = array('rowid' => $rowid, 'qty' => 0);
        $this->cart->update($dados);
        redirect(base_url('carrinho'));
    }

    public function frete_transportadora($estado_destino)
    {
        $peso = 0;
        foreach ($this->cart->contents() as $item) {
            $peso +=
