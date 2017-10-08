<?php
namespace AppBundle\Helper;

use Symfony\Component\HttpFoundation\Request;

class Cliente
{   
    /**
     * padrão de nome valido
     * @var string
     */
    const PATTERN_NOME_COMPLETO = '/^([A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ]+)(\s([A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ]+))+/';
    
    /**
     * padrão de email valido
     * @var string
     */
    const PATTERN_EMAIL = '/^([\w\d])+([\w\d\.\-\_])*\@([\w\d])+([\w\d\.])(\.[\w\d]{2,5}){1,3}/';
    /**
     * Valida form de cadastro e retorna dados limpos
     * @param Request
     * @return array
     */
    static public function getFormCadastro(Request $request)
    {
        $nome = $request->request->get('nome');
        $email = $request->request->get('email');
        if (empty($nome)) {
            throw new \Exception("Nome completo não informado.");
        }
        if (empty($email)) {
            throw new \Exception("E-mail não informado.");
        }
        if (strlen($nome) > 100) {
            throw new \Exception("Nome completo ultrapassa limite de 100 caracteres");
        }
        if (strlen($email) > 100) {
            throw new \Exception("E-mail ultrapassa limite de 100 caracteres");
        }
        self::validaNomeCompleto($nome);
        self::validaEmail($email);
        
        return ['nome' => $nome, 'email' => strtolower($email)];
        
    }
    
    static public function validaNomeCompleto($nome)
    {
        if (!preg_match(self::PATTERN_NOME_COMPLETO, $nome)) {
            throw new \Exception(
                "Nome completo inválido.".
                " Informe o nome e sobrenome, são permitido apenas letras maiusculas e minusculas".
                " e uso de acentuação.");
        }
    }
    
    static public function validaEmail($email)
    {
        if (!preg_match(self::PATTERN_EMAIL, $email)) {
            throw new \Exception(
                "E-mail inválido. ".
                "O email deve seguir o padrão de email válido exemplo@dominio.com.br");
        }
    }
}

