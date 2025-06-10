<?php defined('BASEPATH') OR exit('No direct script access allowed');
/********************************************************************************
* Controlador de cadastros de usuários.
**********************************************************************************/
class Cadastro extends CI_Controller {
    
    private $categorias;
    
    public function __construct() {
        parent::__construct();
        $this->load->model('modelo_categorias', 'modelcategorias');
        $this->categorias = $this->modelcategorias->listar_categorias();
    }
    
    public function index() {     
        $dados_cabecalho['categorias'] = $this->categorias;        
        $this->load->view('cabecalho-html');
        $this->load->view('cabecalho', $dados_cabecalho);
        $this->load->view('novo_cadastro');
        $this->load->view('rodape');
        $this->load->view('rodape-html');
    }
    
    public function adicionar() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('nome', 'Nome', 'required|min_length[5]');
        $this->form_validation->set_rules('cpf', 'CPF', 'required|min_length[14]');
        $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|is_unique[clientes.email]');
        if ($this->form_validation->run() === FALSE) {
            $this->index();
        } else {
            $dados = [
                'nome' => $this->input->post('nome'),
                'sobrenome' => $this->input->post('sobrenome'),
                'rg' => $this->input->post('rg'),
                'cpf' => $this->input->post('cpf'),
                'data_nascimento' => dataBr_to_dataMySQL($this->input->post('data_nascimento')),
                'sexo' => $this->input->post('sexo'),
                'cep' => $this->input->post('cep'),
                'rua' => $this->input->post('rua'),
                'bairro' => $this->input->post('bairro'),
                'cidade' => $this->input->post('cidade'),
                'estado' => $this->input->post('estado'),
                'numero' => $this->input->post('numero'),
                'telefone' => $this->input->post('telefone'),
                'celular' => $this->input->post('celular'),
                'email' => $this->input->post('email'),
                'senha' => $this->input->post('senha')
            ];
            if ($this->db->insert('clientes', $dados)) {
                $this->enviar_email_confirmacao($dados);
            } else {
                echo "Houve um erro ao processar seu cadastro";
            }
        }
    }

    public function enviar_email_confirmacao($dados) {               
        $mensagem = $this->load->view('emails/confirmar_cadastro.php', $dados, true);
        $this->load->library('email');
        $this->email->from("loja@TheGroceryStoreBrazil", "A Mercearia Brasil");
        $this->email->to($dados['email']);
        $this->email->subject('The Grocery Store Brasil - Confirmação de cadastro');
        $this->email->message($mensagem);            
        if ($this->email->send()) {
            $dados_cabecalho['categorias'] = $this->categorias;        
            $this->load->view('cabecalho-html');
            $this->load->view('cabecalho', $dados_cabecalho);
            $this->load->view('cadastro_enviado');
            $this->load->view('rodape');
            $this->load->view('rodape-html');
        } else {
            print_r($this->email->print_debugger());
        }
    }

    public function confirmar($hashEmail) {
        $dados['status'] = 1;    
        $this->db->where('md5(email)', $hashEmail);
        if ($this->db->update('clientes', $dados)) {
            $dados_cabecalho['categorias'] = $this->categorias;
            $this->load->view('cabecalho-html');
            $this->load->view('cabecalho', $dados_cabecalho);
            $this->load->view('cadastro_liberado');
            $this->load->view('rodape');
            $this->load->view('rodape-html');
        } else {
            echo "Houve um erro ao confirmar seu cadastro";
        }
    }
    
    public function formulario_login() {
        $dados_cabecalho['categorias'] = $this->categorias;
        $this->load->view('cabecalho-html');
        $this->load->view('cabecalho', $dados_cabecalho);
        $this->load->view('login');
        $this->load->view('rodape');
        $this->load->view('rodape-html');
    }
    
    public function login() {
        $this->load->library('form_validation');        
        $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');
        $this->form_validation->set_rules('senha', 'Senha', 'required|min_length[5]');
        if ($this->form_validation->run() === FALSE) {
            $this->formulario_login();
        } else {
            $this->db->where('email', $this->input->post('email'));
            $this->db->where('senha', $this->input->post('senha'));
            $this->db->where('status', 1);
            $cliente = $this->db->get('clientes')->result();
            if (count($cliente) == 1) {
                $dadosSessao['cliente'] = $cliente[0];
                $dadosSessao['logado'] = true;
                $this->session->set_userdata($dadosSessao);
                redirect(base_url("produtos"));
            } else {
                $dadosSessao['cliente'] = null;
                $dadosSessao['logado'] = false;
                $this->session->set_userdata($dadosSessao);
                redirect(base_url("login"));
            }
        }
    }   
    
    public function sair() {
        $dadosSessao['cliente'] = null;
        $dadosSessao['logado'] = false;
        $this->session->set_userdata($dadosSessao);
        redirect(base_url("login"));
    }
    
    public function esqueci_minha_senha() {
        $dados_cabecalho['categorias'] = $this->categorias;
        $this->load->view('cabecalho-html');
        $this->load->view('cabecalho', $dados_cabecalho);
        $this->load->view('form_recupera_login');
        $this->load->view('rodape');
        $this->load->view('rodape-html');
    }
    
    public function recuperar_login() {
        $this->load->library('form_validation');        
        $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');
        $this->form_validation->set_rules('cpf', 'CPF', 'required|min_length[5]');
        if ($this->form_validation->run() === FALSE) {
            $this->esqueci_minha_senha();
        } else {
            $this->db->where('email', $this->input->post('email'));
            $this->db->where('cpf', $this->input->post('cpf'));
            $this->db->where('status', 1);
            $cliente = $this->db->get('clientes')->result();
            if (count($cliente) == 1) {
                $dados = (array)$cliente[0];    
                $mensagem = $this->load->view('emails/recuperar_senha.php', $dados, true);
                $this->load->library('email');
                $this->email->from("loja@TheGroceryStoreBrazil", "A Mercearia Brasil");
                $this->email->to($dados['email']);
                $this->email->subject('The Grocery Store Brasil - Recuperação de cadastro');
                $this->email->message($mensagem);            
                if ($this->email->send()) {
                    $dados_cabecalho['categorias'] = $this->categorias;        
                    $this->load->view('cabecalho-html');
                    $this->load->view('cabecalho', $dados_cabecalho);
                    $this->load->view('senha_enviada');
                    $this->load->view('rodape');
                    $this->load->view('rodape-html');
                } else {
                    print_r($this->email->print_debugger());
                }
            } else {
                redirect(base_url("esqueci-minha-senha"));
            }
        }
    }

    public function alterar_cadastro($id) {
        if (null != $this->session->userdata('logado')) {
            $this->db->where('md5(id)', $id);
            $this->db->where('id', $this->session->userdata('cliente')->id);
            $this->db->where('status', 1);
            $dados_corpo['cliente'] = $this->db->get('clientes')->result();
            if (count($dados_corpo['cliente']) == 1) {
                $dados_cabecalho['categorias'] = $this->categorias;        
                $this->load->view('cabecalho-html');
                $this->load->view('cabecalho', $dados_cabecalho);
                $this->load->view('alterar_cadastro', $dados_corpo);
                $this->load->view('rodape');
                $this->load->view('rodape-html');
            } else {
                redirect(base_url("login"));
            }
        } else {
            redirect(base_url("login"));
        }
    }    
    
    public function salvar_alteracao_cadastro() {
        if (null != $this->session->userdata('logado')) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('nome', 'Nome', 'required|min_length[5]');
            $this->form_validation->set_rules('cpf', 'CPF', 'required|min_length[14]');
            $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');
            if ($this->form_validation->run() === FALSE) {
                $this->alterar_cadastro($this->input->post('id'));
            } else {
                $dados = [
                    'nome' => $this->input->post('nome'),
                    'sobrenome' => $this->input->post('sobrenome'),
                    'rg' => $this->input->post('rg'),
                    'cpf' => $this->input->post('cpf'),
                    'data_nascimento' => dataBr_to_dataMySQL($this->input->post('data_nascimento')),
                    'sexo' => $this->input->post('sexo'),
                    'cep' => $this->input->post('cep'),
                    'rua' => $this->input->post('rua'),
                    'bairro' => $this->input->post('bairro'),
                    'cidade' => $this->input->post('cidade'),
                    'estado' => $this->input->post('estado'),
                    'numero' => $this->input->post('numero'),
                    'telefone' => $this->input->post('telefone'),
                    'celular' => $this->input->post('celular'),
                    'email' => $this->input->post('email'),
                    'senha' => $this->input->post('senha'),
                    'status' => 0
                ];
                $this->db->query("INSERT INTO clientes_log SELECT * FROM clientes WHERE md5(id) = '".$this->input->post('id')."'");
                $this->db->where('md5(id)', $this->input->post('id'));
                if ($this->db->update('clientes', $dados)) {
                    $this->enviar_email_confirmacao($dados);
                } else {
                    echo "Houve um erro ao processar seu cadastro";
                }
            }
        } else {
            redirect(base_url('login'));            
        }
    }
    
    public function meus_pedidos() {
        if (null != $this->session->userdata('logado')) {
            $this->load->helper('text');
            $this->load->library('table');
            $this->db->where('cliente', $this->session->userdata('cliente')->id);
            $this->db->order_by('id', 'desc');
            $pedidos = $this->db->get('pedidos')->result();
            $dados['pedidos'] = [];
            foreach ($pedidos as $pedido) {
                $dados['pedidos'][$pedido->id]['pedido'] = $pedido;
                $this->db->select('itens_pedidos.*, produtos.titulo, produtos.descricao');
                $this->db->from('itens_pedidos');
                $this->db->join('produtos', 'itens_pedidos.item = produtos.codigo');
                $this->db->where('itens_pedidos.pedido', $pedido->id);
                $dados['pedidos'][$pedido->id]['itens'] = $this->db->get()->result();
            }
            $dados_cabecalho['categorias'] = $this->categorias;        
            $this->load->view('cabecalho-html');
            $this->load->view('cabecalho', $dados_cabecalho);
            $this->load->view('meus_pedidos', $dados);
            $this->load->view('rodape');
            $this->load->view('rodape-html');
        } else {
            redirect(base_url('login'));
        }
    }
}