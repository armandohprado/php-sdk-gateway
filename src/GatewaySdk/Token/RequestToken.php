<?php

namespace GatewaySdk;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

class SolicitacaoToken {

	public function solicitar(array $dadosSolicitacao) {
		try {
			$client   = new Client(['base_uri' => Getnet::ENDPOINT . 'auth/oauth/v2/token/']);
			$response = $client->request('POST', 'createPaymentID', ['form_params' => $dadosSolicitacao]);

			$retorno = $this->tratarRetornoSolicitacao($response);
		} catch (\Exception $e) {
			$retorno = ['erro' => $e->getMessage(), 'status' => false];
		}

		return $retorno;
	}

	private function tratarRetornoSolicitacao(GuzzleHttp\Psr7\Response $response) {
		$conteudoResposta = $response->getBody()->getContents();

		if (strpos($conteudoResposta, ' ') !== false) {
			return ['erro' => $conteudoResposta, 'status' => false];
		} elseif (empty($conteudoResposta)) {
			return ['erro' => 'A solicitação não retornou resultado', 'status' => false];
		}

		return ['status' => true, 'token' => $conteudoResposta];
	}

	public function montarArrayDadosSolicitacao(array $venda) {
		$post = [];

		$post['orderIdBeta'] = $venda['id_alias'];
		$post['discount']    = $venda['desconto'];
		$post['hideData']    = 1;

		$this->preencherDadosFrete($post, $venda);
		$this->preencherDadosCliente($post, $venda['ClienteCadastro']);
		
		$enderecoEntrega = \Entity\Endereco\Endereco::getEnderecoPorId($venda['id_cliente_cadastro_endereco'], 'ClienteCadastroEndereco');
		$this->preencherDadosEnderecoEntrega($post, $enderecoEntrega);

		$this->preencherDadosItens($post, $venda['Itens']);

		return $post;
	}

	private function preencherDadosFrete(array &$post, array $venda) {
		$post['shippingType'] = \Entity\Frete\Frete::getSiglaTipoFrete($venda['tipo_frete']);
		$post['shippingCost'] = $venda['frete_total'];
	}

	private function preencherDadosCliente(array &$post, array $cliente) {
		$post['senderName']     = $cliente['nome1'];
		$post['senderLastName'] = $cliente['nome2'];
		$post['senderDocument'] = $cliente['documento1'];
		$post['senderType']     = (int) !$cliente['pessoa_fisica'];
		$post['senderEmail']    = $cliente['email'];
		
		$telefone = new \Entity\Telefone\Telefone($cliente['telefone']);
		$post['senderAreaCode'] = $telefone->getDDD();
		$post['senderPhone']    = $telefone->getNumeroSemMascara();
	}

	private function preencherDadosEnderecoEntrega(array &$post, \Entity\Endereco\Endereco $endereco) {
		$post['shippingAddressId']         = $endereco->getId();
		$post['shippingAddressStreet']     = $endereco->getRua();
		$post['shippingAddressNumber']     = $endereco->getNumero();
		$post['shippingAddressComplement'] = $endereco->getComplemento();
		$post['shippingAddressDistrict']   = $endereco->getBairro();
		$post['shippingAddressPostalCode'] = $endereco->getCep();
		$post['shippingAddressCity']       = $endereco->getCidade();
		$post['shippingAddressState']      = $endereco->getEstado();
	}

	private function preencherDadosItens(array &$post, $itens) {
		$indice = 1;

		$model_produto_z3games = \Framework::getFramework()->carregar_model('produto_z3games');
		foreach ($itens as $item) {
			$produto_z3games = $model_produto_z3games->carregar_registros_simples(
				['id_produto_cadastro' => $item['id_produto']], 
				'id, id_produto_z3games', 
				true
			);

			if (empty($produto_z3games)) {
				throw new \Exception('ID do produto na Z3games não econtrado!');
			}

			$post["itemId_{$indice}"]    = $produto_z3games['id_produto_z3games'];
			$post["itemDescr_{$indice}"] = $item['nome'];
			$post["itemQuant_{$indice}"] = $item['quantidade'];
			$post["itemValue_{$indice}"] = $item['preco_unitario'];

			++$indice;
		}
	}
}