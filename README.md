# Sistema de Pagamentos Simplificado

## Técnologias utilizadas
  - PHP 7.4
  - MySQL 5.7
  - Laravel 8
  - Docker

## Rodando o projeto

Certifique-se de ter instalado o docker e docker-compose instalados, e em seguida no terminal digite:

```
docker-compose up --build
```

## Documentação da API
Uma vez que o projeto esteja rodando a documentação estará disponível em:

```
http://localhost:8000/api/docs
```

## Objetivos

Temos 2 tipos de usuários, os comuns e lojistas, ambos têm carteira com dinheiro e realizam transferências entre eles. Vamos nos atentar **somente** ao fluxo de transferência entre dois usuários.

Requisitos:

Esta API se propõe somente a cumprir os resquisitos abaixo:

- Para ambos tipos de usuário, precisamos do Nome Completo, CPF, e-mail e Senha. CPF/CNPJ e e-mails devem ser únicos no sistema. Sendo assim, seu sistema deve permitir apenas um cadastro com o mesmo CPF ou endereço de e-mail.

- Usuários podem enviar dinheiro (efetuar transferência) para lojistas e entre usuários. 

- Lojistas **só recebem** transferências, não enviam dinheiro para ninguém.

- Antes de finalizar a transferência, deve-se consultar um serviço autorizador externo, use este mock para simular (https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6).

- A operação de transferência deve ser uma transação (ou seja, revertida em qualquer caso de inconsistência) e o dinheiro deve voltar para a carteira do usuário que envia. 

- No recebimento de pagamento, o usuário ou lojista precisa receber notificação enviada por um serviço de terceiro e eventualmente este serviço pode estar indisponível/instável. Use este mock para simular o envio (https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04). 

- Este serviço deve ser RESTFul.

De acordo com os requisitos acima, a modelagem de dados se apresenta a seguir:

![alt text](https://github.com/IsaqueRocha/sps/raw/main/.docker/misc/class-diagram.png)


### Payload

A seguir temos um exemplo para efetivar a transação.

POST /transaction

```json
{
    "value" : 100.00,
    "payer" : "5c397c39-f167-468d-846c-b0ac20fa750b",
    "payee" : "44d84301-8b18-437f-b2b7-9587f71ef32e"
}
```

